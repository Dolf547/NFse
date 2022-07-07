<?php
use Utils\System\Database\Enum\StatusNfTransmitidaEnum;
use Utils\System\Database\Enum\StatusNfAutorizadaEnum;
use Utils\System\Database\Enum\TipoNotaEnum;
$redIcon = '<span class="ys-status-icons danger" data-toggle="tooltip" title="Não Autorizada"><i class="material-icons">file_upload</i></span>';
$greenIcon = '<span class="ys-status-icons new" data-toggle="tooltip" title="Autorizada"><i class="material-icons">check_circle</i></span>';
$orangeIcon = '<span class="ys-status-icons attention" data-toggle="tooltip" title="Transmitida"><i class="material-icons">info</i></span>';
$grayIcon = '<span class="ys-status-icons" data-toggle="tooltip" title="Cancelada"><i class="material-icons">cancel</i></span>';
?>
@extends('default')
@section('title', 'Monitor de NFSe')
@include('components.breadcrumbs', [ 
	'icon' => 'dashboard',
	'levelOne' => 'Painel de Controle',
	'secondLevel' => 'NFSe',
	'secondLevelLink' => 'NFSe'
])
@section('css')
<style>
	.fix-fields {
		margin-left: 0px !important;
	}
	.text-center {
		text-align: center !important;
	}
	.table-responsive table {
		margin-bottom: 60px;
	}
	.consultaNfeItens > div h4{
		display: inline;
		font-weight: 600;
	}
	.consultaNfeItens > div span{
		display: inline;
		font-size: 14px;
	}


		@media (max-width: 399px){
			body
			{
				background:#ECEDEF;
			}
			#ys-aside
			{
				background:#424A5D;
			}
			.ys-user__content
		    {
				position: fixed;
			}
			.ys-main-header
			{
			  width: 170%;
			}
			.ys-ambiente 
			{
			font-size:125%;
			line-height: 29px;
			}
			#buscarnotadeservico{
				width:500px;
			}
			#notafiscal{
				width:500px
			}
			.imagens 
			{
    		width: 24px;
			}
			.ys-nav
			{
			background-color: #424A5D;
			}
		}
			
					
		@media screen and (min-width: 403px) and (max-width: 530px){
			body
			{
			background:#ECEDEF;
			}
			#ys-aside
			{
			background:#424A5D;
			}
			.ys-user__content
		    {
			position: fixed;
			}
			.ys-main-header
			{
			width: 163%;
			}
			.ys-ambiente 
			{
			font-size:150%;
			line-height: 37px;
			}
			#buscarnotadeservico
			{
			width:603px;
			}
			#notafiscal
			{
			width:603px;
			}
			.imagens 
			{
  		 	 width: 29px;
			}
			.ys-nav
			{
			background-color: #424A5D;
			}
		}

		@media (max-width: 320px){
			.ys-main-header
		     {
    		  width: 189%;
			 }
			.ys-ambiente 
			{
			font-size:126%;
			}
			.ys-user__content
		    {
			position: fixed;
			}
			.imagens
			{	
			width: 20px;
			}
			.ys-nav
			{
			background-color: #424A5D;
			}
		}

		@media screen and (min-width: 611px) and (max-width: 645px){
			.ys-main-header{
			width: 106%;
			}
			body
			{
			background:#ECEDEF;
			}
			#ys-aside
			{
			background:#424A5D;
			}	
			/* barra de config do perfil */
			.ys-user__content
			{
			position: fixed;
			}
			.ys-nav
			{
			background-color: #424A5D;
			}
		}
</style>
@endsection
@section('main')
<head>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TZ4WHPZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
</head>
<section class="ys-top-box margin-bottom-lg min" id="buscarnotadeservico">
	<div class="row margin-none">
		<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TZ4WHPZ');</script>
<!-- End Google Tag Manager -->

		<form id="filtrosDeNota" method="post" action="?page=1" role="form">
			<div class='row'>
				<div class="col-md-12">
					<header class="ys-header-titles">
						<div class='row'>
							<div class='col-sm-6'>
								<h2 class="ys-title-itens">Busca Nota de Serviço</h2>
							</div>
							<div class='col-sm-6'>
								<button type="submit" name="Limpar" id="Limpar" class="btn btn-warning btn-primary pull-right">Limpar</button>
								<button type="submit" name="Filtrar" id="Filtrar" class="btn btn-submit btn-primary pull-right mr-2">Filtrar</button>
							</div>
						</div>
					</header>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-2">
					<?php echo form_label('Emissão de', 'data_de_emi'); 
					$data = array(
						'name'        => 'data_de_emi',
						'id'          => 'data_de_emi',
						'class'       => 'form-control date-mask',
						'readonly'    => 'readonly',
						'type'        => 'text',
						'placeholder' => 'dd/mm/aaaa',
						'value'       => isset( $_POST['data_de_emi'] ) ? $_POST['data_de_emi'] : date('01/01/Y')
					);
					echo form_input($data) ?>
				</div>
				<div class="col-md-2">
					<?php echo form_label('Emissão até', 'data_ate_emi'); 
					$data = array(
						'name'        => 'data_ate_emi',
						'id'          => 'data_ate_emi',
						'class'       => 'form-control date-mask',
						'readonly'    => 'readonly',
						'type'        => 'text',
						'placeholder' => 'dd/mm/aaaa',
						'value'       => isset( $_POST['data_ate_emi'] ) ? $_POST['data_ate_emi'] : date('d/m/Y')
					);
					echo form_input($data); ?>
				</div>
				<div class="col-2">
					<label for="transmitted_status">Transmissão</label>
					<select class="form-control" name="transmitted_status" id="transmitted_status">
						<option value="">Selecione</option>
						@foreach(StatusNfTransmitidaEnum::mountOption() as $option => $description)
							<option value="{{ $option }}" {{ (old('transmitted_status') == $option ? 'selected' : '')}}>
								{{ $description }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="col-2">
					<label for="authorized_status">Autorização</label>
					<select class="form-control" name="authorized_status" id="authorized_status">
						<option value="">Selecione</option>
						@foreach(StatusNfAutorizadaEnum::mountOption() as $option => $description)
							<option value="{{ $option }}" {{ old('authorized_status') == $option ? 'selected' : ''}}>
								{{ $description }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="col-4">
					<?php echo form_label('Número', 'document_number'); 
					$data = array(
						'name'        => 'document_number',
						'id'          => 'document_number',
						'class'       => 'form-control',
						'type'        => 'text',
						'planeholder' => 'Número do documento',
						'value'       => old('document_number')
					);
					echo form_input($data); ?>
				</div>
				
				<div class="col-3" style=" padding-top: 10px;">
					<!-- <?php callF3('Cliente', 'cliente', '', '', !empty($this->input->post('cliente')) ? $this->input->post('cliente') : '', !empty($this->input->post('ys-cliente-input')) ? $this->input->post('ys-cliente-input') : '','','clientes/adicionar'); ?> -->
					<!-- <?php callF3('Cliente', 'cliente', '', '', !empty($this->input->post('cliente')) ? $this->input->post('cliente') : '', !empty($this->input->post('ys-cliente-input')) ? $this->input->post('ys-cliente-input') : '', '','clientes/adicionar', '', array("tabindex" => 2, "data-camponomefiltro" => "nomecliente", "data-camponomedisplay" => "nome", "data-campoextra-0" => "documento"), true ) ;?> -->
					<?php callF3('Cliente', 'cliente', $label_style, '', '', '', '','clientes/adicionar', '', array("tabindex" => 2, "data-camponomefiltro" => "nomecliente", "data-camponomedisplay" => "nome", "data-campoextra-0" => "documento"), true ) ;?>
				</div>
			</div>
		</form>
	</div>
</section>
<section class="ys-content-box margin-bottom-lg min" id="notafiscal">
	<div class="col-md-12">
		<div class="row margin-none">
			<div class="col-md-12 padding-none">
				<header class="ys-header-titles flex">
					<h2 class="ys-title-itens">Notas Fiscais</h2>
					<div class="ys-box-button">
						<div class="dropdown btn-cad pull-right">
							<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Outras Ações
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<a href="<?php echo base_url('index.php/Ordensdeservico/imprimirOs'); ?>" class="dropdown-item" target="_blank" id="impOs">Imprimir</a>
								<!-- <a href="{{base_url()}}contasareceber/gerarCsvContasReceber{{$cliente}}{{$status}}{{$bandeira}}{{$operadora}}{{$tipo}}{{$data_de}}{{$data_ate}}{{$data_de_emi}}{{$data_ate_emi}}{{$valortotal}}{{$id_empresa}}" class="dropdown-item" target="_blank">Exportar CSV</a> -->
							</div>
						</div>
					</div>
				</header>
			</div>
		</div>
		<div class="col-md-12 padding-none">
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th></th>
							<th>Status</th>
							<th><a href="?order=serie_documento">Série</a></th>
							<th><a href="?order=numero_documento">Número</a></th>
							<th>Retorno</th>
							<th>Número Prefeitura</th>
							<th><a href="?order=data_emissao">Data/Hora</a></th>
							<th><a href="?order=valor_nota">Valor(R$)</a></th>
							<th>Cliente</th>
						</tr>
					</thead>
					<tbody>
						@if(empty($nfs))
							<tr>
								<td colspan="11"><span class="ys-info-empty">Nenhuma Nota Fiscal Cadastrada!</span></td>
							</tr>
						@else
							@foreach($nfs as $nf)
								<tr>
									<td>
										<div class="btn-group" role="group">
											<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="material-icons xs-18">settings</i>
											</button>
											<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
											<a class="dropdown-item" href="#" onclick='abrirResumoVenda("<?=$nf->id_nf_saida?>")'>Resumo da venda</a>
												<a href="!#" onclick="detalhes({{$nf->id_nf_saida}}); $.abrirModalComF3(event,'detalhes')"; title="detalhes" class="dropdown-item btnIcon">
													Detalhes
												</a>
											
												<?php
										
												if ($nf->status_autorizada != StatusNfAutorizadaEnum::AUTORIZADA
													&& $nf->status_transmitida != StatusNfTransmitidaEnum::TRANSMITIDA) {
														 ?>
													<a class="dropdown-item" href="{{ base_url() }}nflocal/transmitir/{{$nf->id_nf_saida}}">Transmitir</a>

												<?php }?>
												<?php  if($nf->status_transmitida == 'nao_transmitida'){ ?>
												
												
											<?php }elseif($nf->status_transmitida == 'transmitida'){ ?>
													<?php if($nf->cancelada != 'S'){?>
													<a class="dropdown-item" href="{{ base_url() }}nflocal/consultar/{{$nf->id_nf_saida}}">Consultar</a>
													<?PHP } ?>
													<?php if($nf->status_transmitida == 'transmitida' && $nf->status_autorizada == 'autorizada'){?>
													 <a class="dropdown-item" href="{{ base_url() }}nflocal/imprimirRPS/{{$nf->id_nf_saida}}" target="_blank">PDF</a>
													<a class="dropdown-item" href="{{ base_url() }}nflocal/verxml/{{$nf->id_nf_saida}}/xml" target="_blank">XML</a>
														<?php } ?>
										<?php	}	?> 
											<?php if($nf->status_transmitida == 'transmitida' && $nf->status_autorizada == 'autorizada'){?>
												<?php if($nf->cancelada == null) { ?>
												<a class="dropdown-item" href="#!" onclick="$.cancelaNFe('{{$nf->id_nf_saida}}');">Cancelar
												<?php }?>
											<?php }?>
											<!-- 	<?php if($nf->status_transmitida == StatusNfTransmitidaEnum::NAO_TRANSMITIDA): ?>
													<?php $urlTransmissao = 'NFSe/transmitir/'.$nf->id_nf_saida; ?>
													<a class="dropdown-item" href="{{ base_url() . $urlTransmissao }}">Transmitir</a>
												<?php endif ?>
												<?php $urlTransmissao = 'NFSe/consultar/'.$nf->id_nf_saida; ?>
												<a class="dropdown-item" href="{{ base_url() . $urlTransmissao }}">Consultar RPS</a>
												<?php if(!empty($nf->xml_enviado) && !empty($nf->xml_recebido)):?>
													<?php $urlPartVerXml = 'NFSe/verXML/'.$nf->id_nf_saida; ?>
													<a class="dropdown-item" href="{{base_url() . $urlPartVerXml}}/xml_enviado" target="_blank">Abrir XML Enviado</a>
													<a class="dropdown-item" href="{{base_url() . $urlPartVerXml}}/xml_recebido" target="_blank">Abrir XML Recebido</a>
												<?php endif ?>
												<?php if($nf->status_transmitida==StatusNfTransmitidaEnum::TRANSMITIDA && !$nf->cancelada) {?>
													<a class="dropdown-item" href="{{ base_url() }}NFSe/imprimirRPS/{{$nf->id_nf_saida}}/{{$nf->especie_nota}}" target="_blank">Imprimir RPS</a>
													<a class="dropdown-item" href="{{ base_url() }}NFSe/cancelar/{{$nf->id_nf_saida}}">Cancelar NFSE</a>
												<?php } ?> -->
												<?php if($nf->status_autorizada == StatusNfAutorizadaEnum::NAO_AUTORIZADA  && $nf->cancelada !== "1"):?>
													<a class="dropdown-item" href="#" onclick='$.excluirNota("<?=$nf->id_nf_saida?>")'>Excluir</a>
												<?php endif; ?>
											</div>
										</div>
									</td>
									<td class="text-center">
										<?php $transmittedStatusIcon = $redIcon;
										if ($nf->cancelada){
											$transmittedStatusIcon = $grayIcon;
										}elseif ($nf->status_autorizada == StatusNfAutorizadaEnum::AUTORIZADA) {
											$transmittedStatusIcon = $greenIcon;
										}elseif ($nf->status_transmitida == StatusNfTransmitidaEnum::TRANSMITIDA) {
											$transmittedStatusIcon = $orangeIcon;
										}
										echo $transmittedStatusIcon; ?>
									</td>
									<td> {{ $nf->serie_documento }} </td>
									<td> {{ $nf->numero_documento }} </td>	
									<td style="max-width:200px"> {{ $nf->mensagem_nota }} </td>
									<td>{{$nf->numeroprefeitura }} </td>
									<td> {{ dateFormat($nf->data_emissao) }}<br/>{{ $nf->hora_emissao }} </td>
									<td> {{ realFormat($nf->valor_nota) }} </td>
									<td>
										@if(isset($nf->nomecliente))
											{{$nf->nomecliente}}
										@elseif(!empty($nf->clicpf))
											{{$nf->clicpf}}
										@else
											Cliente não identificado
										@endif
									</td>
								</tr>
							@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>
{{ $this->pagination->create_links() }}
<div class="modal" id="abrirResumoVenda" tabindex="-1" role="dialog" aria-labelledby="abrirResumoVendaLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content"></div>
	</div>
</div>
<div class="modal" id="detalhes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Detalhes</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-group row">
					<?php $label_style = array('class' => 'col-sm-3 col-form-label middle');
					echo form_label('Chave', 'chave_nfe', $label_style); 
					$data = array(
						'name'        => 'chave_nfe',
						'id'          => 'chave_nfe',
						'class'       => 'form-control',
						'type'        => 'text',
						'disabled'    => 'disabled'
					); ?>
					<div class="col-md-9">
						<?= form_input($data) ?>
					</div> 
				</div>
				<div class="form-group row">
					<?php echo form_label('Protocolo', 'protocolo', $label_style); 
					$data = array(
						'name'        => 'protocolo',
						'id'          => 'protocolo',
						'class'       => 'form-control',
						'type'        => 'text',
						'disabled'    => 'disabled'
					);?>
					<div class="col-md-9">   
						<?= form_input($data) ?>
					</div> 
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('js')
<script>
$('#data_de_emi').datepicker({});
$('#data_ate_emi').datepicker({});
$.excluirNota = function($idNota) {
	swal({
		title: 'Deseja excluir esta nota?',
		text: "Digite abaixo o motivo da exclusão",
		type: 'question',
		input: 'text',
		inputValidator: (value) => {
			if(!value){
				return 'Mensagem de exclusão é obrigatória.'
			}else if(value.length < 15){
				return 'A mensagem tem que ter pelo menos 15 caracteres.'
			}
		},
		showCancelButton: true,
		confirmButtonText: 'Sim, excluir!',
		cancelButtonText: 'Cancelar'
	})
	.then((result) => {
		if (result.value) {
			window.location.href = "{{base_url()}}Nflocal/excluir/"+$idNota+'/'+result.value;
		}
	})
}
function detalhes(id) {
	var self = this;
	$.ajax({
		type: "POST",
		data: {"id": id},
		url: "{{ base_url() }}NFSe/carrega",
		success: function (data) {
			var arrayData = JSON.parse(data);
			$("#protocolo").val(arrayData.protocolo);
			$("#chave_nfe").val(arrayData.chave_nfe);
		},
		error: function (response) {
			alert('erro');
		}
	});
}
function abrirResumoVenda(id_nf_saida){
	$.ajax({
		type: "POST",
		data: {"id_nf_saida": id_nf_saida},
		url: "{{ base_url() }}Nflocal/abrirResumoVenda",
		success: function (data) {
			//console.log(data)//var d = JSON.parse(data);
			$('#abrirResumoVenda .modal-content').html(data);
			$('#abrirResumoVenda').modal('show');
		},
		error: function (response) {
			alert(response.responseText);
		}
	});
}
$.cancelaNFe = function($id_nf_saida){
	swal({
		title: 'Deseja cancelar esta nota?',
		text: "Digite abaixo o motivo do cancelamento para o SEFAZ",
		type: 'warning',
		input: 'text',
		inputValidator: (value) => {
			if(!value){
				return 'Mensagem de cancelamento é obrigatória.'
			}else if(value.length < 15){
				return 'A mensagem tem que ter pelo menos 15 caracteres.'
			}
		},
		showCancelButton: true,
		confirmButtonText: 'Sim, cancelar nota!',
		cancelButtonText: 'Sair'
	})
	.then((result) => { 
		console.log($id_nf_saida);
		if (result.value) {
			$.ajax({
				type: "POST",
				url: "{{base_url()}}Nflocal/cancelar/"+$id_nf_saida,
				data: {id_nf_saida: $id_nf_saida, xJust: result.value},
				success: function(data){
					console.log("oi");
					resp = JSON.parse(data);
					location.reload();
				},
				error: function (response) {
					swal(
						'Erro!',
						'Nota não foi cancelada devido a um erro.',
						'error'
					);
				}
			});
		location.reload();
}
	});
}



</script>

<script>
	var link = document.getElementById("impOs")
	link.setAttribute("href", window.location.href.replace("?", "/imprimeNfseMonitor?") + '?imprime=sim')
</script>
@endsection