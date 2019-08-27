<?php

namespace Notas;

use NFePHP\Common\Certificate;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Standardize;
use Notas\Manifesta;
use Connect\Connect;

class Certificados{

	public function exec($file_dir, $password){

		//Informe o diretorio do certificado
		$pfx 	= file_get_contents($file_dir);

		//Verifica se o certificado está ok
		$cert 	= Certificate::readPfx($pfx, $password);

		return $cert->getCnpj();
	}

	public function verifyCert($file_dir, $password){

		$pfx 	= file_get_contents($file_dir);
		$cert 	= Certificate::readPfx($pfx, $password);

		if(!empty($cert)){

			$vencimento 	 = json_decode(json_encode($cert->getValidTo()), true);
			$status 		 = empty($cert->isExpired()) ? 'VÁLIDO' : 'VENCIDO';
			$empresa_cnpj 	 = !is_numeric($cert->getCnpj())?'' : $cert->getCnpj();
			$empresa_cpf 	 = empty($cert->getCpf())?'' : $cert->getCpf();
			$empresa_nome 	 = $cert->getCompanyName();
			$data_vencimento = date('Y-m-d',strtotime($vencimento['date']));

			$dados = array(
				"empresa_cnpj" => $empresa_cnpj,
				"empresa_cpf"  => $empresa_cpf,
				"empresa_nome" => $empresa_nome,
				"status"   	   => $status,
				"tipo"         => !empty($empresa_cnpj)?'CNPJ':'CPF',
				"vencimento"   => $data_vencimento
			);

		}

		return $dados;

	}

	public function consultaNotas(){
		$busca = new Connect;
		$certificado = $busca->FetchAll('certificado');

		foreach ($certificado as $dados) {
			$dir = DIRECTORY.'pages/contabilidade/'.$dados['diretorio'];
			$password = $dados['senha'];
			$info[] = $this->lote($dir, $password, 10);
		}

		return $info;
	}

	public function lote($file_dir, $password, $limit = 10){

		$pfx 	= file_get_contents($file_dir);
		$busca 	= new Connect;
		//pega os dados do certificado para uso de algumas informações
		$cert 	= Certificate::readPfx($pfx, $password);

		//retorno do Json configurado
		$configJson = $this->configJson($cert);

		$tools = new Tools($configJson, $cert);

/*
|--------------------------------------------------------------------------
| Só funciona para o modelo 55
|--------------------------------------------------------------------------
*/
$tools->model('55');

/*
|--------------------------------------------------------------------------
| Este serviço somente opera em ambiente de produção
|--------------------------------------------------------------------------
*/
$tools->setEnvironment(1);

/*
|--------------------------------------------------------------------------
| $ultNSU
|--------------------------------------------------------------------------
|
| Este numero deverá vir do banco de dados nas proximas buscas para reduzir
| a quantidade de documentos, e para não baixar várias vezes as mesmas
| coisas.
|
*/
$cnpj = $cert->getCnpj();
if(empty($busca->select("nf_ultNSU", "cnpj = $cnpj"))){

	$valor = 0;
	$info_NSU = array(

		"cnpj" => $cnpj,
		"num"  => $valor,
		"dateLastUpdate" => date('d-m-Y H:i:s')
	);

	$busca->Query("nf_ultNSU",$info_NSU, "insert", "");

}else{

	$NSU = $busca->select("nf_ultNSU", "cnpj = $cnpj");
	$valor = $NSU[0]['num'];

}
$ultNSU = (int) $valor;
$maxNSU = $ultNSU;
$loopLimit = $limit;
$iCount = 0;

/*
|--------------------------------------------------------------------------
| Executa a busca de DFe em loop
|--------------------------------------------------------------------------
*/
while ($ultNSU <= $maxNSU) {

	$iCount++;
	if ($iCount >= $loopLimit) {
		break;
	}
	try {
        //executa a busca pelos documentos
		$resp = $tools->sefazDistDFe($ultNSU);
	} catch (\Exception $e) {
		continue;
        //tratar o erro
	}

/*
|--------------------------------------------------------------------------
| Extrair e salvar os retornos
|--------------------------------------------------------------------------
*/
$dom = new \DOMDocument();
$dom->loadXML($resp);
$node = $dom->getElementsByTagName('retDistDFeInt')->item(0);
$tpAmb = $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
$verAplic = $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
$cStat = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
$xMotivo = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
$dhResp = $node->getElementsByTagName('dhResp')->item(0)->nodeValue;
$ultNSU = $node->getElementsByTagName('ultNSU')->item(0)->nodeValue;
$maxNSU = $node->getElementsByTagName('maxNSU')->item(0)->nodeValue;
$lote = $node->getElementsByTagName('loteDistDFeInt')->item(0);
if (empty($lote))
{
    //lote vazio
	continue;
}
/*
|--------------------------------------------------------------------------
| Essas tags irão conter os documentos zipados
|--------------------------------------------------------------------------
*/
$docs = $lote->getElementsByTagName('docZip');

foreach ($docs as $doc) {

	$numnsu 	= $doc->getAttribute('NSU');
	$schema 	= $doc->getAttribute('schema');
	/*
|--------------------------------------------------------------------------
| Envia o último NSU para a base de dados do sistema
|--------------------------------------------------------------------------
*/
$this->enviaNSU($busca, $cnpj, $numnsu);

/*
|--------------------------------------------------------------------------
| Descompacta o documento e recupera o XML original
|--------------------------------------------------------------------------
*/
$content = gzdecode(base64_decode($doc->nodeValue));

/*
|--------------------------------------------------------------------------
| Converte o XML para um ARRAY
|--------------------------------------------------------------------------
*/
$stdCl = new Standardize($content);
$arr = $stdCl->toArray();

/*
|--------------------------------------------------------------------------
| Informa o tipo de documento (procNF, resNFe)
|--------------------------------------------------------------------------
*/
$tipo = substr($schema, 0, 6);

/*
|--------------------------------------------------------------------------
| Envia a nota para ser manifestada caso seja necessária a manifestação
|--------------------------------------------------------------------------
*/


if($schema === 'resNFe_v1.01.xsd')
{
	$chave_nf = $arr['chNFe'];
	if(empty($busca->select("nf", "chave_nf = $chave_nf"))){
		$this->enviaParaManifesto($busca, $chave_nf, $cnpj);
	}
}

/*
|--------------------------------------------------------------------------
| Envia a nota para o diretorio de uploads
|--------------------------------------------------------------------------
*/
if($tipo === 'procNF'){

	$arr = $stdCl->toArray();
	$chave_nf = str_replace('NFe','',$arr['NFe']['infNFe']['attributes']['Id']);

	if(empty($busca->select("nf", "chave_nf = $chave_nf"))){
		$this->enviaXmlsParaDiretorio($content, $stdCl);
	}
}

}

sleep(5); //Tempo de espera para cada loop ( !IMPORTANTE )

}

}

protected function enviaNSU($busca, $cnpj, $ultNSU){

/*
|--------------------------------------------------------------------------
| Verifica se o ultimo NSU existe no banco para o cnpj do loop
| caso não exista será adicionado e se existir ele irá atualizar
|--------------------------------------------------------------------------
*/
if(!empty($busca->select("nf_ultNSU", "cnpj = $cnpj or num = 0 "))){
/*
|--------------------------------------------------------------------------
| Atualiza ultNSU
|--------------------------------------------------------------------------
*/
$info_NSU = array(
	"cnpj" => $cnpj,
	"num"  => $ultNSU,
	"dateLastUpdate" => date('d-m-Y H:i:s')
);

$busca->Query("nf_ultNSU", $info_NSU, "update", "cnpj = '$cnpj'");

}
}

protected function enviaXmlsParaDiretorio($response, $stdCl){
/*
|--------------------------------------------------------------------------
| Cria os arquivos xmls dentro da pasta para uploads das NFe
|--------------------------------------------------------------------------
*/
$arr = $stdCl->toArray();
$chave_nf = str_replace('NFe','',$arr['NFe']['infNFe']['attributes']['Id']);
$filename = '../pages/notas/UPLOADS/xmls/'.$chave_nf.'-nfe.xml';

file_put_contents($filename, $response);

}
protected function enviaParaManifesto($busca, $chave_nf, $cnpj){

	if (empty($busca->select("nf_manifesta", "chave_nf = $chave_nf and cnpj = $cnpj"))) {
		$array = array(
			"cnpj" => $cnpj,
			"chave_nf" => $chave_nf,
			"status" => 'false'
		);
		$busca->Query("nf_manifesta",$array, "insert", "");
	}

}
public function manifestaNotas($dir){

	$busca 	= new Connect;
	$manifesta = $busca->select("nf_manifesta", "status = 'false'");


	foreach ($manifesta as  $manifestar) {

		$id = $manifestar['id'];

		$cnpj = $manifestar['cnpj'];
		$certificado = $busca->select("certificado", "empresa_cnpj = $cnpj");

		$diretorio = $dir.'pages/contabilidade/'.$certificado[0]['diretorio'];
		$senha = $certificado[0]['senha'];

		$pfx = file_get_contents($diretorio);
		$configJson = $this->configJson(Certificate::readPfx($pfx, $senha));

		$tools = new Tools($configJson, Certificate::readPfx($pfx, $senha));
		$tools->model('55');

		$chNFe = $manifestar['chave_nf'];
		$tpEvento = '210210';
		$xJust = '';
		$nSeqEvento = 1;

		$response = $tools->sefazManifesta($chNFe,$tpEvento,$xJust = '',$nSeqEvento = 1);
		$st = new Standardize($response);

		$arr_cStat = $st->toArray();

		if($arr_cStat['cStat'] != '999'){
			$status = "true";
		}else{
			$status = "false";
		}

		$array_dados = array(

			"cnpj" 		=> $cnpj,
			"chave_nf" 	=> $chNFe,
			"status" 	=> $status
		);

		$busca->Query("nf_manifesta", $array_dados, "update", "id = '$id'");
		$st = new Standardize($response);
		$arr = $st->toArray();

		echo '<pre>';
		print_r($arr);
		echo '</pre>';

	}
}


protected function configJson($cert){
/*
|--------------------------------------------------------------------------
| Configuração necessária para a consulta.
|--------------------------------------------------------------------------
*/
$config = [
	"atualizacao" 	=> date('Y-m-d h:i:s'),
	"tpAmb" 		=> 1,
	"razaosocial" 	=> $cert->getCompanyName(),
	"siglaUF" 		=> "SP",
	"cnpj" 			=> $cert->getCnpj(),
	"schemes" 		=> "PL_009_V4",
	"versao" 		=> "4.00",
	"tokenIBPT" 	=> "",
	"CSC"			=>"",
	"CSCid" 		=> "",
	"aProxyConf" => [
		"proxyIp" 	=> "",
		"proxyPort" => "",
		"proxyUser" => "",
		"proxyPass" => ""
	]
];
return json_encode($config);
}

}

?>