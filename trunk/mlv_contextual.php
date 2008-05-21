<?php
/*
Plugin Name: MLV Contextual
Version: 1.5
Plugin URI: http://tecnoblog.net/archives/plugin-mercado-livre-vitrine-contextual-para-wordpress.php
Description: Exibe uma vitrine de ofertas contextuais com anúncios do Mercado Livre em HTML.
Author: Thiago Mobilon
Author URI: http://tecnoblog.net/


Versão 1.3.1 - 24/03/2008
* Novo nome. Agora ao invés de ML Vitrine Contextual, o plugin se chama MLV Contextual.
* O link das imagens agora aponta para uma lista de ofertas. Isto aumentou a conversão significativamente.
* Adicionado a função Fopen. Agora o usuário pode escolher se quer usar Curl ou Fopen.
* Adicionado a opção "Random" para o Filtro1 e Ordenação de ofertas.
* Adicionado um hack, que corrige os bugs com o Parser no Bluehost.
* Adicionada mensagem de Copyright
* Adicionado filtro que remove caracteres inúteis do título das ofertas. Desta forma a vitrine polui menos o blog.


--  Copyright 2007 @ 2008  Thiago Mobilon (contato@tecnoblog.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA,
    or see the website: http://www.gnu.org/philosophy/license-list.html#GPLCompatibleLicenses
*/

$mlv_options=array(
  'mlv_autoshowlocal'=>'under',
  'mlv_cant'=>'3',
  'mlv_encode'=>'n',
  'mlv_preco'=>'y',
  'mlv_encode'=>'n',
  'mlv_function'=>'c',
  'mlv_css'=>'
  /*Vitrine ao todo:*/
  #tabela_ml {
  text-align:center;
  width:100%;
  margin:0;
  }
  
  /*Cada quadrado de oferta*/
  .celula_ml {
  text-align:center;
  padding: 0 3px;
  }
  
  /*Configurações da imagem*/
  .celula_ml img{
  border:none;
  width:90px;
  height:90px;
  margin-bottom:2px;
  }
  
  /*Título das ofertas*/
  .title_ml {
  font-size:12px;
  line-height:120%;
  }
  
  /*Preço das ofertas*/
  .preco_ml {
  color:#3982C6;
  font-size:11px;
  font-weight:700;
  margin:2px 0 0;
  }
'
);

	$options_pms = get_option('mlv_options');
	if (!$options_pms) add_option('mlv_options', $mlv_options);
	else $mlv_options = $options_pms;

// output textarea to easily add tags in admin menu (addition to the post form)
function mlv_contextual_input() {
	global $post;

	$mlv_id = get_post_meta($post->ID, 'mlv_id', true);  
	$mlv_minpr = get_post_meta($post->ID, 'mlv_minpr', true);
	$mlv_word = get_post_meta($post->ID, 'mlv_word', true);
	
	echo '<div id="tagsdiv" class="postbox"><h3><a class="togbox">+</a> MLV_Contextual</h3><div class="inside"><p id="jaxtag"><span id="ajaxtag"><b>Palavra chave</b>:<br/><input type="text" name="mlv_word" id="mlv_word" size="50%" value="' . $mlv_word . '" /><input type="hidden" name="bunny-key" id="bunny-key" value="' . wp_create_nonce('bunny') . '" /><span class="howto">a palavra chave que mais tem a ver com o post</span><br/><b>ID de categoria</b>:<br/><input type="text" name="mlv_id" id="mlv_id" size="50%" value="' . $mlv_id . '" /><input type="hidden" name="bunny-key" id="bunny-key" value="' . wp_create_nonce('bunny') . '" /><span class="howto">ex: "1648" para "Informática" <a href="http://www.mercadolivre.com.br/jm/ml.allcategs.AllCategsServlet" title="Lista de categorias do Mercado Livre">Veja aqui os IDs de cada categoria</a>.</span><br/><b>Pre&ccedil;o m&iacute;nimo</b>:<br/><input type="text" name="mlv_minpr" id="mlv_minpr" size="50%" value="' . $mlv_minpr . '" /><input type="hidden" name="bunny-key" id="bunny-key" value="' . wp_create_nonce('bunny') . '" /><span class="howto">apenas exibir ofertas com valor acima deste</span></span></p><div id="tagchecklist"></div></div></div>';

}

// general custom field update function
function mlv_contextual_update($id)
{

  // authorization
	if ( !current_user_can('edit_post', $id) )
		return $id;
	// origination and intention
	if ( !wp_verify_nonce($_POST['bunny-key'], 'bunny') )
		return $id;
		
	$setting_word = $_POST['mlv_word'];
	$meta_exists = update_post_meta($id, 'mlv_word', $setting_word);
	
	$setting_id = $_POST['mlv_id'];
	$meta_exists = update_post_meta($id, 'mlv_id', $setting_id);
	
	$setting_minpr = $_POST['mlv_minpr'];
	$meta_exists = update_post_meta($id, 'mlv_minpr', $setting_minpr);
	
	if(!$meta_exists)
	{
		add_post_meta($id, 'mlv_word', $setting_word);
		add_post_meta($id, 'mlv_id', $setting_id);
		add_post_meta($id, 'mlv_minpr', $setting_minpr);
	}
}

//Carrega o CSS no Header
function mlv_loadcss() {
         global $mlv_options;
         echo '<style type="text/css" media="screen">'.$mlv_options['mlv_css'].'</style>';
}

//Adiciona a vitrine após os textos
function auto_vc($text){
global $vitrine_ml, $mlv_options;
if((is_single())and($mlv_options["mlv_autoshowlocal"]!='none')){
vitrine_contextual();
  //Acima ou abaixo do post?
  if($mlv_options["mlv_autoshowlocal"]=='over'){
   $text=$vitrine_ml.$text;
   }elseif($mlv_options["mlv_autoshowlocal"]=='under'){
   $text.=$vitrine_ml;
   }
}
return $text;
}

//Função para chamar manualmente
function mlv_contextual(){
global $vitrine_ml, $mlv_options, $s;
if(((is_single())and($mlv_options["mlv_autoshowlocal"]=='none'))||(!empty($s))){
vitrine_contextual();
print $vitrine_ml."\n";
}
}

// Adiciona a opção no menu Options
function mlv_add_options_page() {
		add_options_page('MLV Contextual Options', 'MLV Contextual', 8, basename(__FILE__), 'mlv_manage_options');
}

function startElement($parser, $name, $attrs) { 
global $insideitem, $tag, $encontrados, $vitrine_ml, $mlv_options;
if ($insideitem) {
$tag = $name; 
} elseif ($name == 'ITEM') { 
$insideitem = true;
}
if ($name == 'LISTING') {
$encontrados .= $attrs['ITEMS_TOTAL'];
if ($encontrados == '0') {
$vitrine_ml.= $mlv_options["mlv_anuncio_alternativo"];
}
}
}



function endElement($parser, $name) { 
global $insideitem, $tag, $title, $link, $price, $image, $currency, $encontrados, $actual, $count, $vitrine_ml, $mlv_options,$palabras, $cat; 

if ($name == 'ITEM') { 
$actual++;
$count++;

$link = htmlentities($link, ENT_QUOTES);
$image = htmlentities($image, ENT_QUOTES);

  if($mlv_options["mlv_encode"]=='y'){
    $title = htmlentities($title, ENT_QUOTES);
  }

if(($count=='1')and($encontrados>'0')){$vitrine_ml.= "<table id=\"tabela_ml\" cellpadding=\"0\" cellspacing=\"0\"></tr>";}

     $vitrine_ml.= "<td class=\"celula_ml\">";
	 if($image != '') {$vitrine_ml.="<a href=\"http://pmstrk.mercadolivre.com.br/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&go=http://lista.mercadolivre.com.br/";
	 if(!empty($palabras)){$vitrine_ml.="$palabras";}
	 if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
	 if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
	 $vitrine_ml.="_DisplayType_G\" title=\"Clique para ver e/ou comprar $title\" onClick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"$image\" alt=\"$title\"></a>";
	}else{
     $vitrine_ml.="<a href=\"http://pmstrk.mercadolivre.com.br/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&go=http://lista.mercadolivre.com.br/";
	 if(!empty($palabras)){$vitrine_ml.="$palabras";}
	 if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
	 if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
	 $vitrine_ml.="_DisplayType_G\" title=\"Clique para ver e/ou comprar $title\" onClick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"http://img.mercadolivre.com.br/jm/img?s=MLB&f=artsinfoto.gif&v=I\"></a>";
	}
     $vitrine_ml.="<div class=\"title_ml\">$title<br/><a href=\"$link\" title=\"Mais informa&ccedil;&otilde;es de $title\" onClick=\"javascript: pageTracker._trackPageview('/mlv_contextual/texto');\" rel=\"nofollow\" target=\"_blank\"><b>Mais info&raquo;</b></a></div>";
	 if($mlv_options['mlv_preco']=='y'){
	 $vitrine_ml.="<div class=\"preco_ml\">$currency $price<br/></div>";
	 }
	
	$vitrine_ml.= "</td>";
	
  if($actual == $mlv_options["mlv_ancho"]) { 
  $vitrine_ml.= "</tr>";
  $actual = 0;
  }

	
$title = '';
$link = ''; 
$price = ''; 
$image = ''; 
$currency = ''; 
$insideitem = false;
}
}


function characterData($parser, $data) { 
global $insideitem, $tag, $title, $link, $price, $image, $currency, $mlv_options; 
if ($insideitem) { 
  switch ($tag) { 
  case "TITLE": 
  $title .= str_replace(array('+','/','*','-'),' ',$data);
  $title = str_replace(',',', ',$title);
  $title = trim($title);
  break;
  case "LINK":
  $link .= str_replace("XXX",$mlv_options["mlv_afidml"],$data); 
  $link=trim($link);
  break; 
  case "PRICE": 
  $price .= $data; 
  $price = trim($price); 
  break; 
  case "IMAGE_URL": 
  $image .= $data; 
  $image = trim($image); 
  break; 
  case "CURRENCY": 
  $currency .= $data; 
  $currency = trim($currency); 
  break; 
  } 
} 
}

function vitrine_contextual(){
global $insideitem, $item, $tag, $s, $post, $cat, $palabras, $minpr, $count, $vitrine_ml, $mlv_options, $fil1_array, $fil1_rand, $ord_array, $ord_rand;


#Enable GZip compression? 
$gzip = 'y';

# Other Configs
$insideitem = false; 
$item = array();
$encontrados='';
$tag = '';
$cat='';
$palabras='';
$minpr='';
$executar_ml='';


	if(empty($s)){
	$minpr=urlencode(get_post_meta($post->ID, 'mlv_minpr', true));
	$cat=urlencode(get_post_meta($post->ID, 'mlv_id', true));
	$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
	$palabras=str_replace(array('Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Ï','Ó','Ò','Õ','Ô','Ú','Ù','Û','Ü','Ç'), array('a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','u','u','u','u','c'), $palabras);
	$palabras=str_replace(array('á','à','â','ã','ä','é','è','ê','ë','í','ì','ï','ó','ò','õ','ô','ú','ù','û','ü','ç'), array('a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','u','u','u','u','c'), $palabras);
	$palabras=urlencode($palabras);
	if((empty($palabras))and(empty($cat))){
		$executar_ml=false;
		}else{
		$executar_ml=true;
		}
	}elseif(!empty($s)){
		  $palabras.=trim($s);
		  $palabras=str_replace(array('Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Ï','Ó','Ò','Õ','Ô','Ú','Ù','Û','Ü','Ç'), array('a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','u','u','u','u','c'), $palabras);
		  $palabras=str_replace(array('á','à','â','ã','ä','é','è','ê','ë','í','ì','ï','ó','ò','õ','ô','ú','ù','û','ü','ç'), array('a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','u','u','u','u','c'), $palabras);
		  $palabras=urlencode($palabras);
		  $executar_ml=true;
	}else{
		  $executar_ml=false;
	}

if($executar_ml){
$baseURL = 'http://www.mercadolivre.com.br/jm/searchXml?as_search_both=N&gzip='.$gzip;
if (!empty($cat)){ $baseURL .= '&as_categ_id='.$cat;}
if (!empty($palabras)){ $baseURL .= '&as_word='.$palabras;}
if (!empty($mlv_options["mlv_cant"])){ $baseURL .= '&as_qshow='.$mlv_options["mlv_cant"];}
//Random Filtro 1
if ($mlv_options["mlv_fil1"]=='R'){
	$fil1_array=array('24_HS','PRECIO_FIJO','SOLO_SUBASTAS','UN_PESO','RECIEN_EMPIEZAN','CERTIFIED','NUEVO','USADO','MPAGO');
	$fil1_rand=rand(0,8);
	$baseURL .= '&as_filtro_id='.$fil1_array[$fil1_rand];
	}elseif(!empty($mlv_options["mlv_fil1"]))
	{$baseURL .= '&as_filtro_id='.$mlv_options["mlv_fil1"];}
if (!empty($mlv_options["mlv_fil2"])){ $baseURL .= '&as_filtro_id2='.$mlv_options["mlv_fil2"];}
//Random Ordem
if ($mlv_options["mlv_ord"]=='R'){
	$ord_array=array('AUCTION_STOP','ITEM_TITLE','HIT_PAGE','MENOS_OFERTADOS','MAS_OFERTADOS','BARATOS,CAROS');
	$ord_rand=rand(0,6);
	$baseURL .= '&as_order_id='.$ord_array[$ord_rand];
	}elseif(!empty($mlv_options["mlv_ord"])){ $baseURL .= '&as_order_id='.$mlv_options["mlv_ord"];}
if (!empty($minpr)){ $baseURL .= '&as_price_min='.$minpr;}

	$xml_parser = xml_parser_create('ISO-8859-1'); 
	xml_set_element_handler($xml_parser, "startElement", "endElement"); 
	xml_set_character_data_handler($xml_parser, "characterData"); 
	
	//Selecionar Fopen ou Curl
	
	if($mlv_options['mlv_function']=='c'){
	
		$curl = curl_init();
		$timeout = 0;
	
		curl_setopt ($curl, CURLOPT_URL, $baseURL);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	
		$data = curl_exec($curl);
		xml_parse($xml_parser, $data) or trigger_error("Erro ao executar o parser");
	
	
		curl_close($curl);
	
	}elseif($mlv_options['mlv_function']=='f'){
		
		$fp = fopen($baseURL,"r") or trigger_error("Erro ao executar o parser"); 

		while($data = fread($fp, 4096)) { 
  			# begin parse 
  			xml_parse($xml_parser, $data, feof($fp)) 
  			or die(sprintf("XML error: %s at line %d", 
  			xml_error_string(xml_get_error_code($xml_parser)), 
  			xml_get_current_line_number($xml_parser))); 
  			# end parse 
		}
	
	fclose($fp); 	
	
	}
	
xml_parser_free($xml_parser);

if($count>'0'){
	$vitrine_ml.="</tr><th colspan=\"".$mlv_options["mlv_cant"]."\" style=\"font-size:9px; font-weight:normal; text-align:right;\">Vitrine <a href=\"http://tecnoblog.net/\" title=\"Plugin MLV Contextual para WordPress\" target=\"_blank\">TecnoBlog</a>&nbsp;&nbsp;</a></th>";
	$vitrine_ml.= "</table>";}

}else{

	$vitrine_ml.= $mlv_options["mlv_anuncio_alternativo"];

}
}

// Tela do Painel
function mlv_manage_options() {
  global $mlv_options;
  if (isset($_POST['mlv_atualizar'])) {
	$mlv_options["mlv_autoshowlocal"] = $_POST["mlv_autoshowlocal"];
	$mlv_options["mlv_afidml"] = $_POST["mlv_afidml"];
	$mlv_options["mlv_cant"] = $_POST["mlv_cant"];
	$mlv_options["mlv_ancho"] = $_POST["mlv_ancho"];
	$mlv_options["mlv_ord"] = $_POST["mlv_ord"];
	$mlv_options["mlv_fil1"] = $_POST["mlv_fil1"];
	$mlv_options["mlv_fil2"] = $_POST["mlv_fil2"];
	$mlv_options["mlv_preco"] = $_POST["mlv_preco"];
	$mlv_options["mlv_css"] = $_POST["mlv_css"];
	$mlv_options["mlv_anuncio_alternativo"] = stripslashes($_POST["mlv_anuncio_alternativo"]);
	$mlv_options["mlv_encode"] = $_POST["mlv_encode"];
	$mlv_options['mlv_function'] = $_POST["mlv_function"];
    update_option('mlv_options', $mlv_options);
    ?>
    <div class="updated">
      <p>
        <strong>
          Dados atualizados com sucesso.
        </strong>
      </p>
    </div>
    <?php
  }
  ?>
  <style type="text/css">
  <!--
  label { display:block; width:350px; margin-right:20px; float:left; }
  //-->
  </style>
  <div class="wrap">
    <h2>ML Vitrine Contextual</h2>
      <form method="post">
        <fieldset class="options">
		  <label for="mlv_autoshowlocal">Exibir a vitrine automaticamente:</label>
		  <select name="mlv_autoshowlocal" id="mlv_autoshowlocal">
		    <option <?php if($mlv_options['mlv_autoshowlocal'] == 'over') { echo 'selected'; } ?> value="over">Acima do post</option>
        	<option <?php if($mlv_options['mlv_autoshowlocal'] == 'under') { echo 'selected'; } ?> value="under">Abaixo do post</option>
        	<option <?php if($mlv_options['mlv_autoshowlocal'] == 'none') { echo 'selected'; } ?> value="none">Vou inserir manualmente</option>
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_afidml">ID de Afiliado Mercado Livre:</label>
          <input name="mlv_afidml" type="text" id="mlv_afidml" value="<?=$mlv_options['mlv_afidml'];?>" size="25" maxlength="25" />
          <br />
		  <br/>
		  <label for="mlv_cant">Quantidade de ofertas a exibir:</label>
		  <select name="mlv_cant" id="mlv_cant">
		    <option <?php if($mlv_options['mlv_cant'] == '0') { echo 'selected'; } ?> value="0">Selecione</option>
        	<option <?php if($mlv_options['mlv_cant'] == '1') { echo 'selected'; } ?> value="1">1</option>
			<option <?php if($mlv_options['mlv_cant'] == '2') { echo 'selected'; } ?> value="2">2</option>
			<option <?php if($mlv_options['mlv_cant'] == '3') { echo 'selected'; } ?> value="3">3</option>
			<option <?php if($mlv_options['mlv_cant'] == '4') { echo 'selected'; } ?> value="4">4</option>
			<option <?php if($mlv_options['mlv_cant'] == '5') { echo 'selected'; } ?> value="5">5</option>
			<option <?php if($mlv_options['mlv_cant'] == '6') { echo 'selected'; } ?> value="6">6</option>
			<option <?php if($mlv_options['mlv_cant'] == '7') { echo 'selected'; } ?> value="7">7</option>
			<option <?php if($mlv_options['mlv_cant'] == '8') { echo 'selected'; } ?> value="8">8</option>
			<option <?php if($mlv_options['mlv_cant'] == '9') { echo 'selected'; } ?> value="9">9</option>
			<option <?php if($mlv_options['mlv_cant'] == '10') { echo 'selected'; } ?> value="10">10</option>
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_ancho">Quantidade de ofertas por linha:</label>
		  <select name="mlv_ancho" id="mlv_ancho">
		    <option <?php if($mlv_options['mlv_ancho'] == '0') { echo 'selected'; } ?> value="0">Selecione</option>
        	<option <?php if($mlv_options['mlv_ancho'] == '1') { echo 'selected'; } ?> value="1">1</option>
			<option <?php if($mlv_options['mlv_ancho'] == '2') { echo 'selected'; } ?> value="2">2</option>
			<option <?php if($mlv_options['mlv_ancho'] == '3') { echo 'selected'; } ?> value="3">3</option>
			<option <?php if($mlv_options['mlv_ancho'] == '4') { echo 'selected'; } ?> value="4">4</option>
			<option <?php if($mlv_options['mlv_ancho'] == '5') { echo 'selected'; } ?> value="5">5</option>
			<option <?php if($mlv_options['mlv_ancho'] == '6') { echo 'selected'; } ?> value="6">6</option>
			<option <?php if($mlv_options['mlv_ancho'] == '7') { echo 'selected'; } ?> value="7">7</option>
			<option <?php if($mlv_options['mlv_ancho'] == '8') { echo 'selected'; } ?> value="8">8</option>
			<option <?php if($mlv_options['mlv_ancho'] == '9') { echo 'selected'; } ?> value="9">9</option>
			<option <?php if($mlv_options['mlv_ancho'] == '10') { echo 'selected'; } ?> value="10">10</option>
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_ord">Ordenar as ofertas por:</label>
		  <select name="mlv_ord" id="mlv_ord">
		    <option <?php if($mlv_options['mlv_ord'] == '') { echo 'selected'; } ?> value="">Selecione</option>
        	<option <?php if($mlv_options['mlv_ord'] == 'AUCTION_STOP') { echo 'selected'; } ?> value="AUCTION_STOP">Tempo restante</option>
			<option <?php if($mlv_options['mlv_ord'] == 'ITEM_TITLE') { echo 'selected'; } ?> value="ITEM_TITLE">Alfabeticamente</option>
			<option <?php if($mlv_options['mlv_ord'] == 'HIT_PAGE') { echo 'selected'; } ?> value="HIT_PAGE">Visitas</option>
			<option <?php if($mlv_options['mlv_ord'] == 'MENOS_OFERTADOS') { echo 'selected'; } ?> value="MENOS_OFERTADOS">Menos vendidos</option>
			<option <?php if($mlv_options['mlv_ord'] == 'MAS_OFERTADOS') { echo 'selected'; } ?> value="MAS_OFERTADOS">Mais vendidos</option>
			<option <?php if($mlv_options['mlv_ord'] == 'BARATOS') { echo 'selected'; } ?> value="BARATOS">Mais baratos</option>
			<option <?php if($mlv_options['mlv_ord'] == 'CAROS') { echo 'selected'; } ?> value="CAROS">Mais caros</option>
			<option <?php if($mlv_options['mlv_ord'] == 'R') { echo 'selected'; } ?> value="R">Rand&ocirc;mico</option>
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_fil1">Filtro 1 - exibir somente ofertas que sejam:</label>
		  <select name="mlv_fil1" id="mlv_fil1">
		    <option <?php if($mlv_options['mlv_fil1'] == '') { echo 'selected'; } ?> value="">Nenhum</option>
        	<option <?php if($mlv_options['mlv_fil1'] == '24_HS') { echo 'selected'; } ?> value="24_HS">Finalizam em 24h</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'PRECIO_FIJO') { echo 'selected'; } ?> value="PRECIO_FIJO">Pre&ccedil;o fixo</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'SOLO_SUBASTAS') { echo 'selected'; } ?> value="SOLO_SUBASTAS">Leil&atilde;o</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'UN_PESO') { echo 'selected'; } ?> value="UN_PESO">Come&ccedil;am em R$1</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'RECIEN_EMPIEZAN') { echo 'selected'; } ?> value="RECIEN_EMPIEZAN">Come&ccedil;am hoje</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'CERTIFIED') { echo 'selected'; } ?> value="CERTIFIED">MercadoL&iacute;deres</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'NUEVO') { echo 'selected'; } ?> value="NUEVO">Produtos Novos</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'USADO') { echo 'selected'; } ?> value="USADO">Produtos Usados</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'MPAGO') { echo 'selected'; } ?> value="MPAGO">Aceita Mercado Pago</option>	
			<option <?php if($mlv_options['mlv_fil1'] == 'R') { echo 'selected'; } ?> value="R">Rand&ocirc;mico</option>	
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_fil2">Filtro 2 - exibir somente ofertas que sejam:</label>
		  <select name="mlv_fil2" id="mlv_fil2">
		    <option <?php if($mlv_options['mlv_fil2'] == '') { echo 'selected'; } ?> value="">Nenhum</option>
        	<option <?php if($mlv_options['mlv_fil2'] == '24_HS') { echo 'selected'; } ?> value="24_HS">Finalizam em 24h</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'PRECIO_FIJO') { echo 'selected'; } ?> value="PRECIO_FIJO">Pre&ccedil;o fixo</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'SOLO_SUBASTAS') { echo 'selected'; } ?> value="SOLO_SUBASTAS">Leil&atilde;o</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'UN_PESO') { echo 'selected'; } ?> value="UN_PESO">Come&ccedil;am em R$1</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'RECIEN_EMPIEZAN') { echo 'selected'; } ?> value="RECIEN_EMPIEZAN">Come&ccedil;am hoje</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'CERTIFIED') { echo 'selected'; } ?> value="CERTIFIED">MercadoL&iacute;deres</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'NUEVO') { echo 'selected'; } ?> value="NUEVO">Produtos Novos</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'USADO') { echo 'selected'; } ?> value="USADO">Produtos Usados</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'MPAGO') { echo 'selected'; } ?> value="MPAGO">Aceita Mercado Pago</option>	
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_preco">Exibir o pre&ccedil;o das ofertas?</label>
		  <select name="mlv_preco" id="mlv_preco">
		    <option <?php if($mlv_options['mlv_preco'] == 'y') { echo 'selected'; } ?> value="y">Sim</option>
        	<option <?php if($mlv_options['mlv_preco'] == 'n') { echo 'selected'; } ?> value="n">N&atilde;o</option>
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_css">C&oacute;digo CSS. Aqui você pode alterar o visual dos an&uacute;ncios:</label>
          <textarea name="mlv_css" id="mlv_css" cols="70" rows="30" ><?=stripslashes($mlv_options['mlv_css']);?></textarea>
          <br/>
		  <br/>
          <label for="mlv_anuncio_alternativo">An&uacute;ncio alternativo: (ex: código do Adsense, SmartAd, etc)</label>
          <textarea name="mlv_anuncio_alternativo" id="mlv_anuncio_alternativo" cols="70" rows="6" ><?=stripslashes($mlv_options['mlv_anuncio_alternativo']);?></textarea>
          <br/>
		  <br/>
		  <label for="mlv_encode">Habilitar Encoding? (S&oacute; mude se as ofertas estiverem aparecendo com problemas na acentua&ccedil;&atilde;o)</label>
		  <select name="mlv_encode" id="mlv_encode">
        	<option <?php if($mlv_options['mlv_encode'] == 'y') { echo 'selected'; } ?> value="y">Sim</option>
			<option <?php if($mlv_options['mlv_encode'] == 'n') { echo 'selected'; } ?> value="n">N&atilde;o</option>
		  </select>
		  <br/>
		  <br/>
		  <label for="mlv_function">Selecione uma fun&ccedil;&atilde;o (Novamente, s&oacute; mude se houver algum erro com uma das fun&ccedil;&otilde;es)</label>
		  <select name="mlv_function" id="mlv_function">
        	<option <?php if($mlv_options['mlv_function'] == 'c') { echo 'selected'; } ?> value="c">curl()</option>
			<option <?php if($mlv_options['mlv_function'] == 'f') { echo 'selected'; } ?> value="f">fopen()</option>
		  </select>
		  <br/>
		  <br/>
           <div class="submit" style="text-align: left;margin-top:10px">
            <input type="submit" name="mlv_atualizar" value="Atualizar" />
          </div>
        </fieldset>
      </form>
  </div>
<?php
}

// Actions and Filters
add_action('admin_menu', 'mlv_add_options_page');
add_filter('the_content', 'auto_vc');
add_action('wp_head', 'mlv_loadcss');
add_action('simple_edit_form', 'mlv_contextual_input');
add_action('edit_form_advanced', 'mlv_contextual_input');
add_action('edit_page_form', 'mlv_contextual_input');
add_action('edit_post', 'mlv_contextual_update');
add_action('publish_post', 'mlv_contextual_update');
add_action('save_post', 'mlv_contextual_update');
?>