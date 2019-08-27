
<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
define('DIR', __DIR__);
require_once 'controller/system.php';
?>
	<!-- Meta tags Obrigatórias -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="assets/custom.css">
	<link href="https://fonts.googleapis.com/css?family=Lexend+Exa&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="assets/css/fileupload.css">
	<script type="text/javascript" src="assets/js/dropzone.js"></script>

	<title>CONSULTA DE NOTAS</title>
</head>
<body>
	<!-- Image and text -->
	<nav class="navbar navbar-expand-lg navbar-light bg-silver">
		<a class="navbar-brand" href="#">CONSULTAR</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Features</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Pricing</a>
				</li>
			</ul>
			<span class="navbar-text">
				Navbar text with an inline element
			</span>
		</div>
	</nav>


<div class="container">
<!-- HTML heavily inspired by https://blueimp.github.io/jQuery-File-Upload/ -->
<div id="actions" class="row">
    <div class="col-lg-7">
      <!-- The fileinput-button span is used to style the file input field as button -->
      <span class="btn btn-success fileinput-button dz-clickable">
          <i class="glyphicon glyphicon-plus"></i>
          <span>Add files...</span>
      </span>
      <button type="submit" class="btn btn-primary start">
          <i class="glyphicon glyphicon-upload"></i>
          <span>Start upload</span>
      </button>
      <button type="reset" class="btn btn-warning cancel">
          <i class="glyphicon glyphicon-ban-circle"></i>
          <span>Cancel upload</span>
      </button>
    </div>

    <div class="col-lg-5">
      <!-- The global file processing state -->
      <span class="fileupload-process">
        <div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress=""></div>
        </div>
      </span>
    </div>
</div>
<div class="table table-striped files" id="previews">
    <div id="template" class="file-row dz-image-preview">
        <!-- This is used as the file preview template -->
        <div>
            <span class="preview"><img data-dz-thumbnail></span>
        </div>
        <div>
            <p class="name" data-dz-name></p>
            <strong class="error text-danger" data-dz-errormessage></strong>
        </div>
        <div>
            <p class="size" data-dz-size></p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
            </div>
        </div>
        <div>
            <button class="btn btn-primary start">
                <i class="glyphicon glyphicon-upload"></i>
                <span>Start</span>
            </button>
            <button data-dz-remove class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span>Cancel</span>
            </button>
            <button data-dz-remove class="btn btn-danger delete">
                <i class="glyphicon glyphicon-trash"></i>
                <span>Delete</span>
            </button>
        </div>
    </div>
</div>
</div>



	<div class="container p-4">
		<form action="" method="POST" enctype="multipart/form-data">

			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text">Enviar Certificado</span>
				</div>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="certificado" name="certificado">
					<label class="custom-file-label" for="inputGroupFile01">Escolher arquivo</label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-check-label" for="exampleCheck1">Check me out</label>
				<select class="form-control" id="estado" name="estado">
					<option value="AC">Acre</option>
					<option value="AL">Alagoas</option>
					<option value="AP">Amapá</option>
					<option value="AM">Amazonas</option>
					<option value="BA">Bahia</option>
					<option value="CE">Ceará</option>
					<option value="DF">Distrito Federal</option>
					<option value="ES">Espírito Santo</option>
					<option value="GO">Goiás</option>
					<option value="MA">Maranhão</option>
					<option value="MT">Mato Grosso</option>
					<option value="MS">Mato Grosso do Sul</option>
					<option value="MG">Minas Gerais</option>
					<option value="PA">Pará</option>
					<option value="PB">Paraíba</option>
					<option value="PR">Paraná</option>
					<option value="PE">Pernambuco</option>
					<option value="PI">Piauí</option>
					<option value="RJ">Rio de Janeiro</option>
					<option value="RN">Rio Grande do Norte</option>
					<option value="RS">Rio Grande do Sul</option>
					<option value="RO">Rondônia</option>
					<option value="RR">Roraima</option>
					<option value="SC">Santa Catarina</option>
					<option value="SP">São Paulo</option>
					<option value="SE">Sergipe</option>
					<option value="TO">Tocantins</option>
					<option value="EX">Estrangeiro</option>
				</select>
			</div>
			<div class="form-group">
				<label>ENVIAR CERTIFICADO</label>
				<input class="form-control" type="file" name="certificado" />

			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
	<!-- JavaScript (Opcional) -->
	<!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
	<script type="text/javascript" src="assets/js/fileupload.js"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>
</html>