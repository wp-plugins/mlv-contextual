<?php
/*
Plugin Name: MLV Contextual
Version: 2.1.6
Plugin URI: http://www.tecnoblog.net/117402/mlv-contextual-wordpress/
Description: Exibe uma vitrine de ofertas contextuais com anúncios do Mercado Livre em HTML.
Author: Thiago Mobilon
Author URI: http://tecnoblog.net/

Versão 2.1.5 - 03/2013
* Suporte ao ML Colombia
* Widgets :D

Versão 2.1.4 - 12/2012
* Correção de bug de duplicação da vitrine

Versão 2.1 - 10/2012
* Atualizado para a nova API do MercadoLivre

Versão 2.0b1 - 11 e 12/2009
* Nova função para limpar Keywords
* Auto Fopen/Curl
* Multipaís
* Tradução área de admin para espanhol
* Func. MLV apenas em posts antigos
* Funções do loop xml voltaram para o arquivo principal
* Quando nenhuma palavra chave é especificada, o MLV_Contextual automaticamente exibe as ofertas mais buscadas / vendidas do Mercado Livre;
* FALTA: caixa mover admin / CSS Style Vert / Multiple styles / JS Styles

Versão 2.0a1 - 11/2008
* Correção do Bug onde as imagens não apareciam em alguns hosts
* Inclusão de parcelamento Mercado Pago
* Deleta os custom fields quando o post é deletado. DB fica limpa.
* Correção do Bug que duplica custom fields
* CSS em arquivo separado para deixar a página mais leve
* Alterado o título da vitrine em "Settings"
* Funções do loop xml colocadas em um arquivo separado

Versão 1.3.1 - 24/03/2008
* Novo nome. Agora ao invés de ML Vitrine Contextual, o plugin se chama MLV Contextual.
* O link das imagens agora aponta para uma lista de ofertas. Isto aumentou a conversão significativamente.
* Adicionado a função Fopen. Agora o usuário pode escolher se quer usar Curl ou Fopen.
* Adicionado a opção "Random" para o Filtro1 e Ordenação de ofertas.
* Adicionado um hack, que corrige os bugs com o Parser no Bluehost.
* Adicionada mensagem de Copyright
* Adicionado filtro que remove caracteres inúteis do título das ofertas. Desta forma a vitrine polui menos o blog.


--  Copyright 2007 @ 2009  Thiago Mobilon (mobilon@tecnoblog.net)

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
  'mlv_pais'=>'mlb',
  'mlv_ctxwords'=>'mlvc',
  'mlv_vititle'=>'Ofertas Relacionadas',
  'mlv_time' => '0',
  'mlv_autoshowlocal'=>'under',
  'mlv_cant'=>'3',
  'mlv_encode'=>'n',
  'mlv_encode'=>'n',
  'mlv_function'=>'c',
);

	$options_pms = get_option('mlv_options');
	if (!$options_pms) add_option('mlv_options', $mlv_options);
	else $mlv_options = $options_pms;

$pais = $mlv_options['mlv_pais'];

	switch ($pais) {
	case 'mla':
	$urlml = 'mercadolibre.com.ar';
	$urlmlista ='api.mercadolibre.com/sites/MLA/';
	$urlgo ='listado.mercadolibre.com.ar';
	include_once ('lang/es_AR.php');
	break;
	case 'mlb':
	$urlml = 'mercadolivre.com.br';
	$urlmlista ='api.mercadolibre.com/sites/MLB/';
	$urlgo ='lista.mercadolivre.com.br';
	include_once ('lang/pt_BR.php');
	break;
	case 'mlc':
	$urlml = 'mercadolibre.cl';
	$urlmlista ='api.mercadolibre.com/sites/MLC/';
	$urlgo ='listado.mercadolibre.cl';
	include_once ('lang/es_AR.php');
	break;
	case 'mlo':
	$urlml = 'mercadolibre.com.co';
	$urlmlista ='api.mercadolibre.com/sites/MCO/';
	$urlgo ='listado.mercadolibre.com.co';
	include_once ("lang/es_AR.php");
	break;
	case 'mlm':
	$urlml = 'mercadolibre.com.mx';
	$urlmlista ='api.mercadolibre.com/sites/MLM/';
	$urlgo ='listado.mercadolibre.com.mx';
	include_once ('lang/es_AR.php');
	break;
	case 'mlv':
	$urlml = 'mercadolibre.com.ve';
	$urlmlista ='api.mercadolibre.com/sites/MLV/';
	$urlgo ='listado.mercadolibre.com.ve';
	include_once ('lang/es_AR.php');
	break;
		default:
		$urlml = 'mercadolivre.com.br';
		$urlmlista ='api.mercadolibre.com/sites/MLB/';
		$urlgo ='lista.mercadolivre.com.br';
		include_once ('lang/pt_BR.php');
		break;
	}

// output textarea to easily add tags in admin menu (addition to the post form)
add_action('admin_menu', 'mlv_input_custom_box');
function mlv_input_custom_box() {
	add_meta_box( 'mlv_input_sectionid', 'MLV_Contextual', 
	            'mlv_input_inner_custom_box', 'post');
}

function mlv_input_inner_custom_box() {
	global $post, $pais, $urlml, $urlmlista, $lang;

	$mlv_id = get_post_meta($post->ID, 'mlv_id', true);  
	$mlv_minpr = get_post_meta($post->ID, 'mlv_minpr', true);
	$mlv_word = get_post_meta($post->ID, 'mlv_word', true);
	
	echo "<p id=\"jaxtag\"><span id=\"ajaxtag\"><b>".$lang['palavra-chave']."</b>:<br/><input type=\"text\" name=\"mlv_word\" id=\"mlv_word\" size=\"50%\" value=\"".$mlv_word."\" /><input type=\"hidden\" name=\"bunny-key\" id=\"bunny-key\" value=\"".wp_create_nonce("bunny")."\" /><span class=\"howto\">".$lang['palavra-mais-tem']."</span><br/><b>ID de categoria</b>:<br/><input type=\"text\" name=\"mlv_id\" id=\"mlv_id\" size=\"50%\" value=\"".$mlv_id."\" /><input type=\"hidden\" name=\"bunny-key\" id=\"bunny-key\" value=\"".wp_create_nonce("bunny")."\" /><span class=\"howto\">".$lang['1648-info']." <a href=\"http://www.".$urlml."/jm/ml.allcategs.AllCategsServlet\" title=\"".$lang['lista-categorias']."\">".$lang['veja-ids']."</a>.</span><br/><b>".$lang['preco-min']."</b>:<br/><input type=\"text\" name=\"mlv_minpr\" id=\"mlv_minpr\" size=\"50%\" value=\"".$mlv_minpr."\" /><input type=\"hidden\" name=\"bunny-key\" id=\"bunny-key\" value=\"".wp_create_nonce("bunny")."\" /><span class=\"howto\">".$lang['apenas-acima']."</span></span></p>";
}

//Function to clean Keywords
function trat($var){
 
 $var = strtolower($var);
 $var = trim($var);
 $var = ereg_replace("[áàâãª]","a",$var); 
 $var = ereg_replace("[éèê]","e",$var); 
 $var = ereg_replace("[íìî]","i",$var); 
 $var = ereg_replace("[óòôõº]","o",$var); 
 $var = ereg_replace("[úùû]","u",$var); 
 $var = str_replace("ç","c",$var);
 $var = str_replace("_"," ",$var);
 $var = str_replace("-"," ",$var);
 $var = str_replace(",","",$var);
 $var = str_replace("&","e",$var);
 $var = str_replace("?","",$var);
 $var = str_replace('"','',$var);
 $var = str_replace('/','',$var);
 $var = str_replace("'","",$var);
 
 return $var;
 
}

// general custom field update function
add_action('save_post', 'mlv_contextual_update', 1, 2);
add_action('edit_post', 'mlv_contextual_update');
add_action('publish_post', 'mlv_contextual_update');
function mlv_contextual_update($post_id) {
	global $mlv_options, $count_keywords, $topkw_key, $pais, $urlml, $urlmlista;

  // authorization
	if ( !current_user_can('edit_post', $post_id) )
		return $post_id;
	// origination and intention
	if ( !wp_verify_nonce($_POST['bunny-key'], 'bunny') )
		return $post_id;
	
	/*
	//hack para corrigir duplicados
	if (!$post || $post->post_type == 'revision') {
	return;
	}*/
	
	$setting_word = trim($_POST['mlv_word']);
	$setting_id = trim($_POST['mlv_id']);
	$setting_minpr = trim($_POST['mlv_minpr']);
	
	if(!update_post_meta($post_id, 'mlv_word', $setting_word))
	{
		add_post_meta($post_id, 'mlv_word', $setting_word, true);
	}
	if(!update_post_meta($post_id, 'mlv_id', $setting_id))
	{
		add_post_meta($post_id, 'mlv_id', $setting_id, true);
	}
	if(!update_post_meta($post_id, 'mlv_minpr', $setting_minpr))
	{
		add_post_meta($post_id, 'mlv_minpr', $setting_minpr, true);
	}
	
	//caso não haja nenhuma palavra chave, ou ID para o post em questão
	if((($setting_word)and($setting_id))==''){
		
		//executar script que coleta as top keywords
		$xml_parserkey = xml_parser_create();
		xml_set_element_handler($xml_parserkey, "startElementkey", "endElementkey");
		xml_set_character_data_handler($xml_parserkey, "characterDatakey");

		$baseURL2 = "http://www.".$urlml."/jm/ml.web.pulse.PulsePageController?gzip=y&as_XML";

	//Selecionar Fopen ou Curl
	
	if(function_exists(curl_init)){
	
		$curl = curl_init();
		$timeout = 0;
	
		curl_setopt ($curl, CURLOPT_URL, $baseURL2);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	
		$data = curl_exec($curl);
		xml_parse($xml_parserkey, $data) or trigger_error("Erro ao executar o parser");
	
	
		curl_close($curl);
	
	}else{
		
		$fp = fopen($baseURL2,"r") or trigger_error("Erro ao executar o parser"); 

		while($data = fread($fp, 4096)) { 
  			# begin parse 
  			xml_parse($xml_parserkey, $data, feof($fp)) 
  			or die(sprintf("XML error: %s at line %d", 
  			xml_error_string(xml_get_error_code($xml_parserkey)), 
  			xml_get_current_line_number($xml_parserkey))); 
  			# end parse 
		}
	
	fclose($fp); 	
	
	}
		xml_parser_free($xml_parserkey);
	}

}

// Function:Deleta os custom fields
add_action('delete_post', 'mlv_delete_cfields');
function mlv_delete_cfields($post_ID) {
	global $wpdb;
	delete_post_meta($post_ID, 'mlv_word');
	delete_post_meta($post_ID, 'mlv_id');
	delete_post_meta($post_ID, 'mlv_minpr');	
}

//Carrega o CSS no Header
add_action('wp_head', 'mlv_loadcss');
function mlv_loadcss() {
         global $mlv_options;
		 echo '<link rel="stylesheet" href="'.get_bloginfo('url').'/wp-content/plugins/mlv-contextual/mlv_stylesheet.css" type="text/css" media="screen" />'."\n";
}

//BEGIN LOOP
//include_once ('functions_loop.php');

//Funções para gerar o loop coletor de palavras chave

	$count_keywords='1';
	$topkw_key='';
	$count='0';

function startElementkey($parserkey, $name, $attrs) {
	global $tag2, $attrbs, $keyword2;
	$tag2 = $name;
}
	
function endElementkey($parserkey, $name) {
	global $keyword2, $tag2, $count_keywords, $topkw_key;
	if(($name=='KEYWORD')and($count_keywords<='5')){
		$topkw_key='_mlv_topkw'.$count_keywords;
		if(!update_post_meta('1', $topkw_key, $keyword2)){
		add_post_meta('1', $topkw_key, $keyword2, true);
		}
		$keyword2='';
		$count_keywords++;
		$topkw_key='';
	}
}

function characterDatakey($parserkey, $data) {
	global $keyword2, $tag2;
	if ($tag2=="KEYWORD") {
	  $keyword2 .= $data;
	  $keyword2=trim($keyword2);
	  }
}

//Funções para gerar o loop coletor de ofertas

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
global $insideitem, $tag, $title, $link, $price, $image, $currency, $encontrados, $actual, $count, $vitrine_ml, $mlv_options,$palabras, $cat, $mpago, $pais, $urlml, $urlmlista, $urlgo, $lang; 

if ($name == 'ITEM') { 
$actual++;
$count++;

$link = htmlentities($link, ENT_QUOTES);
$image = htmlentities($image, ENT_QUOTES);

  if($mlv_options["mlv_encode"]=='y'){
    $title = htmlentities($title, ENT_QUOTES);
  }

if(($count=='1')and($encontrados>'0')){$vitrine_ml.= "<table id=\"tabela_ml\" cellpadding=\"0\" cellspacing=\"0\"><tr><th class=\"mlv_vititle\" colspan=\"".$mlv_options["mlv_cant"]."\">".$mlv_options['mlv_vititle']."</th></tr>";}

     $vitrine_ml.= "<td class=\"celula_ml\">";
	 if($image != '') {$vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;go=http://".$urlmlista."/";
	 if(!empty($palabras)){$vitrine_ml.="$palabras";}
	 if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
	 if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
	 $vitrine_ml.="_DisplayType_G\" title=\"".$lang["clique"]." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"$image\" alt=\"$title\" /></a>";
	}else{
     $vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;go=http://".$urlmlista."/";
	 if(!empty($palabras)){$vitrine_ml.="$palabras";}
	 if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
	 if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
	 $vitrine_ml.="_DisplayType_G\" title=\"".$lang["clique"]." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"http://img.mercadolivre.com.br/jm/img?s=".$pais."&f=artsinfoto.gif&v=I\" /></a>";
	}
     $vitrine_ml.="<div class=\"title_ml\">$title<br/><a href=\"$link\" title=\"".$lang['mais-info']." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/texto');\" rel=\"nofollow\" target=\"_blank\"><b>Mais info&raquo;</b></a></div>";
	 
	 $vitrine_ml.="<div class=\"preco_ml\">$currency $price<br /></div>";
	 
	 
	 if($mpago=='Y') {
	 	$price_jur=str_replace(",","",$price);
	 	$price_jur=($price_jur*1.35)/18;
	 	$price_jur=number_format($price_jur, 2, ".", "");
	 	$vitrine_ml.="<div class=\"mpago_ml\">".$lang['ate-18']." $price_jur<br /></div>";
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
$mpago='';
}
}


function characterData($parser, $data) { 
global $insideitem, $tag, $title, $link, $price, $image, $currency, $mlv_options, $mpago; 
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
  case "MPAGO":
  $mpago .= $data; 
  $mpago = trim($mpago);
  break;
  } 
} 
}

// END LOOP

//Adiciona a vitrine após os textos
function auto_vc($text){
global $vitrine_ml, $mlv_options, $pais, $lang, $mlv_post_time, $mlv_time;

if((is_single())and($mlv_options["mlv_autoshowlocal"]!='none')){
	$mlv_post_time = get_the_time('U');
	$mlv_time = trim($mlv_options['mlv_time']);
	if($mlv_time=='0') {
		vitrine_contextual();
		//Acima ou abaixo do post?
		if($mlv_options["mlv_autoshowlocal"]=='over'){
		$text=$vitrine_ml.$text;
		}elseif($mlv_options["mlv_autoshowlocal"]=='under'){
		$text.=$vitrine_ml;
		}
	} elseif ($mlv_post_time<=time()-($mlv_time*24*60*60)){
		vitrine_contextual();
		//Acima ou abaixo do post?
		if($mlv_options["mlv_autoshowlocal"]=='over'){
		$text=$vitrine_ml.$text;
		}elseif($mlv_options["mlv_autoshowlocal"]=='under'){
		$text.=$vitrine_ml;
		}
	}
}
return $text;
}

//Função para chamar manualmente
function mlv_contextual(){
global $vitrine_ml, $mlv_options, $s, $pais, $lang, $mlv_post_time, $mlv_time;

	if((is_single())and($mlv_options["mlv_autoshowlocal"]=='none')){
		$mlv_post_time = get_the_time('U');
		$mlv_time = $mlv_options['mlv_time'];
		if($mlv_time=='0') {
		vitrine_contextual();
		print $vitrine_ml."\n";
		} elseif ($mlv_post_time<=time()-($mlv_time*24*60*60)){
		vitrine_contextual();
		print $vitrine_ml."\n";
		}
	}elseif(!empty($s)){
		vitrine_contextual();
		print $vitrine_ml."\n";
	}
}

// Adiciona a opção no menu Options
function mlv_add_options_page() {
		add_options_page('MLV Contextual Options', 'MLV Contextual', 8, basename(__FILE__), 'mlv_manage_options');
}

function widget_contextual($mlv_cant, $mlv_ancho) {
	global $insideitem, $item, $tag, $s, $post, $cat, $palabras, $minpr, $count, $vitrine_ml, $mlv_options, $fil1_array, $fil1_rand, $ord_array, $ord_rand, $mpago, $pais, $urlml, $urlmlista, $lang, $urlgo, $executou;
	
	$vitrine_ml = '';
	
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

	# COMECO - edicao para suporte do palavras de e moneticao - COMECO
		# inclusao by bernabauer.com

		if($mlv_options['mlv_ctxwords'] == 'mlvc') {
			$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
		 } else {
			$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
			$current_plugins = get_option('active_plugins'); 
			if (!in_array('palavras-de-monetizacao/palavrasmonetizacao.php', $current_plugins)) { 
				$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
			} else {
	$array_pm = pm_get_words();
	$palabras.= str_replace  ( " "  , "+"  , $array_pm[0] );
			}
		}
	# FIM - edicao para suporte do palavras de e moneticao - FIM

		$palabras= trat($palabras);
		$palabras=urlencode($palabras);
		if((empty($palabras))and(empty($cat))){
			$executar_ml=false;
			}else{
			$executar_ml=true;
			}
		}elseif(!empty($s)){
			  $palabras = trat($palabras);
			  $palabras = urlencode($palabras);
			  $executar_ml=true;
		}else{
			  $executar_ml=false;
		}

		$cnt = 1;

			$baseURL = "https://".$urlmlista."search?";
			if (!empty($cat)){ $baseURL .= '&category='.$cat;}
			if (!empty($palabras)){ $baseURL .= '&q='.$palabras;}
			
			if ($palabras == "") $baseURL = 'https://api.mercadolibre.com/sites/MLB/search?category=1051&FilterID=relevance';

			if (function_exists('curl_init')) {
				$curl = curl_init();
				$timeout = 100;

				curl_setopt ($curl, CURLOPT_URL, $baseURL);
				curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);

				$data = curl_exec($curl);

				curl_close($curl);
			} else {
				$fp = fopen($baseURL,"r") or trigger_error("Erro ao executar o parser"); 

				while($data = fread($fp, 4096)) { 
		  			# begin parse 
		  			echo $data;
		  			# end parse 
				}

				fclose($fp);
			}

			$data = json_decode($data);

			$qtd = count($data->results);

			if ($qtd > 0) {

				$vitrine_ml .= "<table cellpadding=\"0\" cellspacing=\"0\">";

				$linha = 1;

				if ($mlv_cant > 4) $vitrine_ml.= '<tr>';

				foreach ($data->results as $prod) {

					if ($minpr != '' && !($prod->price >= $minpr)) { $vitrine_ml.=''; }
					else {

						$vitrine_ml.= "<td class=\"celula_ml\" style=\"padding:8px 0;" . ($cnt != $mlv_cant ? "border-bottom:1px solid #ededed;" : "") . "\">";
						 if($prod->thumbnail != '') {$vitrine_ml.="<a style=\"float:left;\" href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink;
						 //if(!empty($palabras)){$vitrine_ml.="$palabras";}
						 //if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
						 //if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
						 $vitrine_ml.="\" title=\"".$lang["clique"]." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"$prod->thumbnail\" alt=\"$prod->title\" /></a>";
						}else{
					     $vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink;
						 //if(!empty($palabras)){$vitrine_ml.="$palabras";}
						 //if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
						 //if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
						 $vitrine_ml.="\" title=\"".$lang["clique"]." $prod->title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"http://img.mercadolivre.com.br/jm/img?s=".$pais."&f=artsinfoto.gif&v=I\" /></a>";
						}
					     $vitrine_ml.="<div style=\"margin-left:90px;\"><div class=\"title_ml\">$prod->title<br/><a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink."\" title=\"".$lang['mais-info']." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/texto');\" rel=\"nofollow\" target=\"_blank\"><b>Mais info&raquo;</b></a></div>";

						 $vitrine_ml.="<div class=\"preco_ml\" style=\"margin:0;padding:0;\">" . str_replace(array('BRL', 'ARS', 'CLP', 'MXN', 'VEF'), array('R$', '$', '$', '$', 'BsF'), $prod->currency_id) . " $prod->price<br /></div>";

						if (isset($prod->installments->quantity)) {
							$vitrine_ml.=/*"<div class=\"mpago_ml\" style=\"margin:0;padding:0;\">até ".$prod->installments->quantity."x de ".str_replace(array('BRL'), array('R$'), $prod->currency_id).' '.$prod->installments->amount."</div>"*/"</div>";
						}

						if ($linha == $mlv_ancho) { $vitrine_ml.= '</tr>'; }

						if ($mlv_cant == $cnt) break;

						if ($linha == $mlv_ancho) { $vitrine_ml.= '<tr>'; $linha = 1; }
						else { $linha++; }

						$cnt++;

					}

				}

				$vitrine_ml.= "</table>";

			}
		
		echo $vitrine_ml;
	
}

function vitrine_contextual(){
global $insideitem, $item, $tag, $s, $post, $cat, $palabras, $minpr, $count, $vitrine_ml, $mlv_options, $fil1_array, $fil1_rand, $ord_array, $ord_rand, $mpago, $pais, $urlml, $urlmlista, $lang, $urlgo, $executou;

if ($executou == true && $sidebar != true) { return false; }

$mlv_cant = $mlv_options["mlv_cant"];
$mlv_ancho = $mlv_options["mlv_ancho"];

$executou = true;
$executouSide = true;

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

# COMECO - edicao para suporte do palavras de e moneticao - COMECO
	# inclusao by bernabauer.com
	
	if($mlv_options['mlv_ctxwords'] == 'mlvc') {
		$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
	 } else {
		$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
		$current_plugins = get_option('active_plugins'); 
		if (!in_array('palavras-de-monetizacao/palavrasmonetizacao.php', $current_plugins)) { 
			$palabras.=trim(get_post_meta($post->ID, 'mlv_word', true));
		} else {
$array_pm = pm_get_words();
$palabras.= str_replace  ( " "  , "+"  , $array_pm[0] );
		}
	}
# FIM - edicao para suporte do palavras de e moneticao - FIM

	$palabras= trat($palabras);
	$palabras=urlencode($palabras);
	if((empty($palabras))and(empty($cat))){
		$executar_ml=false;
		}else{
		$executar_ml=true;
		}
	}elseif(!empty($s)){
		  $palabras = trat($palabras);
		  $palabras = urlencode($palabras);
		  $executar_ml=true;
	}else{
		  $executar_ml=false;
	}
	
	$cnt = 1;

	if ($executar_ml) {
		
		$baseURL = "https://".$urlmlista."search?";
		if (!empty($cat)){ $baseURL .= '&category='.$cat;}
		if (!empty($palabras)){ $baseURL .= '&q='.$palabras;}
		
		if (function_exists('curl_init')) {
			$curl = curl_init();
			$timeout = 100;

			curl_setopt ($curl, CURLOPT_URL, $baseURL);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);

			$data = curl_exec($curl);

			curl_close($curl);
		} else {
			$fp = fopen($baseURL,"r") or trigger_error("Erro ao executar o parser"); 

			while($data = fread($fp, 4096)) { 
	  			# begin parse 
	  			echo $data;
	  			# end parse 
			}

			fclose($fp);
		}
		
		$data = json_decode($data);
		
		$qtd = count($data->results);
		
		if ($qtd > 0) {
			$vitrine_ml.= "<table id=\"tabela_ml\" cellpadding=\"0\" cellspacing=\"0\"><tr><th class=\"mlv_vititle\" colspan=\"".$mlv_cant."\">".$mlv_options['mlv_vititle']."</th></tr>";
			
			$linha = 1;
			
			if ($mlv_cant > 4) $vitrine_ml.= '<tr>';
			
			foreach ($data->results as $prod) {
				
				if ($minpr != '' && !($prod->price >= $minpr)) { $vitrine_ml.=''; }
				else {
					
					$vitrine_ml.= "<td class=\"celula_ml\">";
					 if($prod->thumbnail != '') {$vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink;
					 //if(!empty($palabras)){$vitrine_ml.="$palabras";}
					 //if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
					 //if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
					 $vitrine_ml.="\" title=\"".$lang["clique"]." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"$prod->thumbnail\" alt=\"$prod->title\" /></a>";
					}else{
				     $vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink;
					 //if(!empty($palabras)){$vitrine_ml.="$palabras";}
					 //if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
					 //if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
					 $vitrine_ml.="\" title=\"".$lang["clique"]." $prod->title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"http://img.mercadolivre.com.br/jm/img?s=".$pais."&f=artsinfoto.gif&v=I\" /></a>";
					}
				     $vitrine_ml.="<div class=\"title_ml\">$prod->title<br/><a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink."\" title=\"".$lang['mais-info']." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/texto');\" rel=\"nofollow\" target=\"_blank\"><b>Mais info&raquo;</b></a></div>";

					 $vitrine_ml.="<div class=\"preco_ml\" style=\"margin:0;padding:0;\">" . str_replace(array('BRL', 'ARS', 'CLP', 'MXN', 'VEF'), array('R$', '$', '$', '$', 'BsF'), $prod->currency_id) . " $prod->price<br /></div>";

					if (isset($prod->installments->quantity)) {
						$vitrine_ml.="<div class=\"mpago_ml\" style=\"margin:0;padding:0;\">até ".$prod->installments->quantity."x de ".str_replace(array('BRL'), array('R$'), $prod->currency_id).' '.$prod->installments->amount."</div>";
					}

					if ($mlv_cant > 4 && $linha == $mlv_ancho) { $vitrine_ml.= '</tr>'; }

					if ($mlv_cant == $cnt) break;

					if ($mlv_cant > 4 && $linha == $mlv_ancho) { $vitrine_ml.= '<tr>'; $linha = 1; }
					else { $linha++; }

					$cnt++;
					
				}
				
			}
			
			if($cnt-1!=0){
				$vitrine_ml.="<tr><th class=\"powered_by\" colspan=\"".$mlv_cant."\">Powered by <a href=\"http://www.tecnoblog.net/117402/mlv-contextual-wordpress/\" title=\"Plugin MLV Contextual para WordPress\" target=\"_blank\">MLV Contextual</a>&nbsp;&nbsp;</th></tr>";
				$vitrine_ml.= "</table>";}
			
		}
		
	}
	
	if((!empty($mlv_options["mlv_anuncio_alternativo"])) && ($cnt-1 == 0)){

		//Anúncio alternativo
		$vitrine_ml.= $mlv_options["mlv_anuncio_alternativo"];
	
	} elseif ($cnt-1 == 0) {
		
		// https://api.mercadolibre.com/sites/MLB/search?category=1051&FilterID=relevance
		
		$baseURL = 'https://api.mercadolibre.com/sites/MLB/search?category=1051&FilterID=relevance';
		
		if (function_exists('curl_init')) {
			$curl = curl_init();
			$timeout = 100;

			curl_setopt ($curl, CURLOPT_URL, $baseURL);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);

			$data = curl_exec($curl);

			curl_close($curl);
		} else {
			$fp = fopen($baseURL,"r") or trigger_error("Erro ao executar o parser"); 

			while($data = fread($fp, 4096)) { 
	  			# begin parse 
	  			echo $data;
	  			# end parse 
			}

			fclose($fp);
		}
		
		$data = json_decode($data);
		
		$qtd = count($data->results);
		
		$cnt = 1;
		
		if ($qtd > 0) {
			$vitrine_ml.= "<table id=\"tabela_ml\" cellpadding=\"0\" cellspacing=\"0\"><tr><th class=\"mlv_vititle\" colspan=\"".$mlv_cant."\">".$mlv_options['mlv_vititle']."</th></tr>";
			
			$linha = 1;
			
			if ($mlv_cant > 4) $vitrine_ml.= '<tr>';
			
			foreach ($data->results as $prod) {
				
				if ($minpr != '' && !($prod->price >= $minpr)) { $vitrine_ml.=''; }
				else {
					
					$vitrine_ml.= "<td class=\"celula_ml\">";
					 if($prod->thumbnail != '') {$vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink;
					 //if(!empty($palabras)){$vitrine_ml.="$palabras";}
					 //if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
					 //if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
					 $vitrine_ml.="\" title=\"".$lang["clique"]." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"$prod->thumbnail\" alt=\"$prod->title\" /></a>";
					}else{
				     $vitrine_ml.="<a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink;
					 //if(!empty($palabras)){$vitrine_ml.="$palabras";}
					 //if(!empty($cat)){$vitrine_ml.="_CategID_$cat";}
					 //if(!empty($minpr)){$vitrine_ml.="_PriceMin_$minpr";}
					 $vitrine_ml.="\" title=\"".$lang["clique"]." $prod->title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/imagem');\" rel=\"nofollow\" target=\"_blank\"><img src=\"http://img.mercadolivre.com.br/jm/img?s=".$pais."&f=artsinfoto.gif&v=I\" /></a>";
					}
				     $vitrine_ml.="<div class=\"title_ml\">$prod->title<br/><a href=\"http://pmstrk.".$urlml."/jm/PmsTrk?tool=".$mlv_options["mlv_afidml"]."&amp;word=pmsressellerTMOBILON&amp;go=".$prod->permalink."\" title=\"".$lang['mais-info']." $title\" onclick=\"javascript: pageTracker._trackPageview('/mlv_contextual/texto');\" rel=\"nofollow\" target=\"_blank\"><b>Mais info&raquo;</b></a></div>";

					 $vitrine_ml.="<div class=\"preco_ml\" style=\"margin:0;padding:0;\">" . str_replace(array('BRL', 'ARS', 'CLP', 'MXN', 'VEF'), array('R$', '$', '$', '$', 'BsF'), $prod->currency_id) . " $prod->price<br /></div>";

					if (isset($prod->installments->quantity)) {
						$vitrine_ml.="<div class=\"mpago_ml\" style=\"margin:0;padding:0;\">até ".$prod->installments->quantity."x de ".str_replace(array('BRL'), array('R$'), $prod->currency_id).' '.$prod->installments->amount."</div>";
					}

					if ($mlv_cant > 4 && $linha == $mlv_ancho) { $vitrine_ml.= '</tr>'; }

					if ($mlv_cant == $cnt) break;

					if ($mlv_cant > 4 && $linha == $mlv_ancho) { $vitrine_ml.= '<tr>'; $linha = 1; }
					else { $linha++; }

					$cnt++;
					
				}
				
			}
			
			if($cnt-1!=0){
				$vitrine_ml.="<tr><th class=\"powered_by\" colspan=\"".$mlv_cant."\">Powered by <a href=\"http://www.tecnoblog.net/117402/mlv-contextual-wordpress/\" title=\"Plugin MLV Contextual para WordPress\" target=\"_blank\">MLV Contextual</a>&nbsp;&nbsp;</th></tr>";
				$vitrine_ml.= "</table>";}
			
		}
		
	}
	
}

// Tela do Painel
function mlv_manage_options() {
  global $mlv_options, $lang;
  if (isset($_POST['mlv_atualizar'])) {
 	$mlv_options["mlv_pais"] = $_POST["mlv_pais"];
	$mlv_options["mlv_ctxwords"] = $_POST["mlv_ctxwords"];
	$mlv_options["mlv_autoshowlocal"] = $_POST["mlv_autoshowlocal"];
	$mlv_options["mlv_time"] = $_POST["mlv_time"];	
	$mlv_options["mlv_afidml"] = $_POST["mlv_afidml"];
	$mlv_options["mlv_vititle"] = $_POST["mlv_vititle"];
	$mlv_options["mlv_cant"] = $_POST["mlv_cant"];
	$mlv_options["mlv_ancho"] = $_POST["mlv_ancho"];
	$mlv_options["mlv_ord"] = $_POST["mlv_ord"];
	$mlv_options["mlv_fil1"] = $_POST["mlv_fil1"];
	$mlv_options["mlv_fil2"] = $_POST["mlv_fil2"];
	$mlv_options["mlv_anuncio_alternativo"] = stripslashes($_POST["mlv_anuncio_alternativo"]);
	$mlv_options["mlv_encode"] = $_POST["mlv_encode"];
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
    <h2>MLV Contextual</h2>
      <form method="post">
	  
    <table class="form-table">
	 <tr>
		<th scope="row" valign="top">País:</th>
		<td>
		  <select name="mlv_pais" id="mlv_pais">
		    <option <?php if($mlv_options['mlv_pais'] == 'mla') { echo 'selected'; } ?> value="mla">Argentina</option>
        	<option <?php if($mlv_options['mlv_pais'] == 'mlb') { echo 'selected'; } ?> value="mlb">Brasil</option>
        	<option <?php if($mlv_options['mlv_pais'] == 'mlc') { echo 'selected'; } ?> value="mlc">Chile</option>
			<option <?php if($mlv_options['mlv_pais'] == 'mlo') { echo 'selected'; } ?> value="mlo">Colômbia</option>
        	<option <?php if($mlv_options['mlv_pais'] == 'mlm') { echo 'selected'; } ?> value="mlm">México</option>
        	<option <?php if($mlv_options['mlv_pais'] == 'mlv') { echo 'selected'; } ?> value="mlv">Venezuela</option>
		  </select>
		</td>
	 </tr>
	</table>

	<?php/*
    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['origem-cont']; ?>:</th>
		<td>
		  <select name="mlv_ctxwords" id="mlv_ctxwords">
		    <option <?php if($mlv_options['mlv_ctxwords'] == 'mlvc') { echo 'selected'; } ?> value="mlvc">MLV Contextual</option>
        	<option <?php if($mlv_options['mlv_ctxwords'] == 'pm') { echo 'selected'; } ?> value="pm"><?php echo $lang['pals-monet']; ?></option>
		  </select> <br /> 
			<?php 
				$current_plugins = get_option('active_plugins'); 
				if (!in_array('palavras-de-monetizacao/palavrasmonetizacao.php', $current_plugins)) { 
			
			echo $lang['necess-plugin'];
		
		} else { 

			echo $lang['apto-pmon'];
		
		} ?>
		</td>
	 </tr>
	</table>
	*/?>

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['id-af-ml']; ?>:</th>
		<td>
          <input name="mlv_afidml" type="text" id="mlv_afidml" value="<?=$mlv_options['mlv_afidml'];?>" size="25" maxlength="25" />
		</td>
	 </tr>
	</table>
	
	    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['exibir-auto']; ?>:</th>
		<td>
		  <select name="mlv_autoshowlocal" id="mlv_autoshowlocal">
		    <option <?php if($mlv_options['mlv_autoshowlocal'] == 'over') { echo 'selected'; } ?> value="over"><?php echo $lang['acima-post']; ?></option>
        	<option <?php if($mlv_options['mlv_autoshowlocal'] == 'under') { echo 'selected'; } ?> value="under"><?php echo $lang['abaixo-post']; ?></option>
        	<option <?php if($mlv_options['mlv_autoshowlocal'] == 'none') { echo 'selected'; } ?> value="none"><?php echo $lang['vou-manual']; ?></option>
		  </select>
		</td>
	 </tr>
	</table>
	
	    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['dias']; ?>:</th>
		<td>
		  <select name="mlv_time" id="mlv_time">
		    <option <?php if($mlv_options['mlv_time'] == '0') { echo 'selected'; } ?> value="0"><?php echo $lang['0']; ?></option>
        	<option <?php if($mlv_options['mlv_time'] == '7') { echo 'selected'; } ?> value="7"><?php echo $lang['7']; ?></option>
        	<option <?php if($mlv_options['mlv_time'] == '15') { echo 'selected'; } ?> value="15"><?php echo $lang['15']; ?></option>
			<option <?php if($mlv_options['mlv_time'] == '30') { echo 'selected'; } ?> value="30"><?php echo $lang['30']; ?></option>
		  </select>
		</td>
	 </tr>
	</table>
	
	<table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['title-vitr']; ?>:</th>
		<td>
          <input name="mlv_vititle" type="text" id="mlv_vititle" value="<?=$mlv_options['mlv_vititle'];?>" size="40" maxlength="40" />
		</td>
	 </tr>
	</table>

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['quant-ofertas']; ?>:</th>
		<td>
		  <select name="mlv_cant" id="mlv_cant">
		    <option <?php if($mlv_options['mlv_cant'] == '0') { echo 'selected'; } ?> value="0"><?php echo $lang['selecione']; ?></option>
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

		</td>
	 </tr>
	</table>

    <table class="form-table">
		</td>
	 </tr>
	</table>

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['quant-ofertas-linha']; ?>:</th>
		<td>
		  <select name="mlv_ancho" id="mlv_ancho">
		    <option <?php if($mlv_options['mlv_ancho'] == '0') { echo 'selected'; } ?> value="0"><?php echo $lang['selecione']; ?></option>
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
		</td>
	 </tr>
	</table>

	<?php /*
    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['ord-por']; ?>:</th>
		<td>
		  <select name="mlv_ord" id="mlv_ord">
		    <option <?php if($mlv_options['mlv_ord'] == '') { echo 'selected'; } ?> value=""><?php echo $lang['selecione']; ?></option>
        	<option <?php if($mlv_options['mlv_ord'] == 'AUCTION_STOP') { echo 'selected'; } ?> value="AUCTION_STOP"><?php echo $lang['temp-rest']; ?></option>
			<option <?php if($mlv_options['mlv_ord'] == 'ITEM_TITLE') { echo 'selected'; } ?> value="ITEM_TITLE"><?php echo $lang['alfab']; ?></option>
			<option <?php if($mlv_options['mlv_ord'] == 'HIT_PAGE') { echo 'selected'; } ?> value="HIT_PAGE">Visitas</option>
			<option <?php if($mlv_options['mlv_ord'] == 'MENOS_OFERTADOS') { echo 'selected'; } ?> value="MENOS_OFERTADOS">Menos vendidos</option>
			<option <?php if($mlv_options['mlv_ord'] == 'MAS_OFERTADOS') { echo 'selected'; } ?> value="MAS_OFERTADOS"><?php echo $lang['mais-ven']; ?></option>
			<option <?php if($mlv_options['mlv_ord'] == 'BARATOS') { echo 'selected'; } ?> value="BARATOS"><?php echo $lang['mais-bar']; ?></option>
			<option <?php if($mlv_options['mlv_ord'] == 'CAROS') { echo 'selected'; } ?> value="CAROS"><?php echo $lang['mais-car']; ?></option>
			<option <?php if($mlv_options['mlv_ord'] == 'R') { echo 'selected'; } ?> value="R"><?php echo $lang['rand']; ?></option>
		  </select>
		</td>
	 </tr>
	</table>

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['filtro-1']; ?>:</th>
		<td>
		  <select name="mlv_fil1" id="mlv_fil1">
		    <option <?php if($mlv_options['mlv_fil1'] == '') { echo 'selected'; } ?> value=""><?php echo $lang['nenhum']; ?></option>
        	<option <?php if($mlv_options['mlv_fil1'] == '24_HS') { echo 'selected'; } ?> value="24_HS"><?php echo $lang['fin-24h']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'PRECIO_FIJO') { echo 'selected'; } ?> value="PRECIO_FIJO"><?php echo $lang['preco-fix']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'SOLO_SUBASTAS') { echo 'selected'; } ?> value="SOLO_SUBASTAS"><?php echo $lang['leilao']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'UN_PESO') { echo 'selected'; } ?> value="UN_PESO"><? echo $lang['comec-1']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'RECIEN_EMPIEZAN') { echo 'selected'; } ?> value="RECIEN_EMPIEZAN"><?php echo $lang['comec-hoje']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'CERTIFIED') { echo 'selected'; } ?> value="CERTIFIED">MercadoL&iacute;deres</option>
			<option <?php if($mlv_options['mlv_fil1'] == 'NUEVO') { echo 'selected'; } ?> value="NUEVO"><?php echo $lang['prods-novs']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'USADO') { echo 'selected'; } ?> value="USADO"><?php echo $lang['prods-usads']; ?></option>
			<option <?php if($mlv_options['mlv_fil1'] == 'MPAGO') { echo 'selected'; } ?> value="MPAGO"><?php echo $lang['aceitam-mp']; ?></option>	
			<option <?php if($mlv_options['mlv_fil1'] == 'R') { echo 'selected'; } ?> value="R"><?php echo $lang['rand']; ?></option>	
		  </select>
		</td>
	 </tr>
	</table>

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['filtro-2']; ?>:</th>
		<td>
		  <select name="mlv_fil2" id="mlv_fil2">
		    <option <?php if($mlv_options['mlv_fil2'] == '') { echo 'selected'; } ?> value=""><?php echo $lang['nenhum']; ?></option>
        	<option <?php if($mlv_options['mlv_fil2'] == '24_HS') { echo 'selected'; } ?> value="24_HS"><?php echo $lang['fin-24h']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'PRECIO_FIJO') { echo 'selected'; } ?> value="PRECIO_FIJO"><?php echo $lang['preco-fix']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'SOLO_SUBASTAS') { echo 'selected'; } ?> value="SOLO_SUBASTAS"><?php echo $lang['leilao']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'UN_PESO') { echo 'selected'; } ?> value="UN_PESO"><? echo $lang['comec-1']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'RECIEN_EMPIEZAN') { echo 'selected'; } ?> value="RECIEN_EMPIEZAN"><?php echo $lang['comec-hoje']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'CERTIFIED') { echo 'selected'; } ?> value="CERTIFIED">MercadoL&iacute;deres</option>
			<option <?php if($mlv_options['mlv_fil2'] == 'NUEVO') { echo 'selected'; } ?> value="NUEVO"><?php echo $lang['prods-novs']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'USADO') { echo 'selected'; } ?> value="USADO"><?php echo $lang['prods-usads']; ?></option>
			<option <?php if($mlv_options['mlv_fil2'] == 'MPAGO') { echo 'selected'; } ?> value="MPAGO"><?php echo $lang['aceitam-mp']; ?></option>	
		  </select>
		</td>
	 </tr>
	</table>
*/?>
    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['anunc-alt']; ?></th>
		<td>
          <textarea name="mlv_anuncio_alternativo" id="mlv_anuncio_alternativo" cols="70" rows="6" ><?=stripslashes($mlv_options['mlv_anuncio_alternativo']);?></textarea>
		</td>
	 </tr>
	</table>

    <table class="form-table">
	 <tr>
		<th scope="row" valign="top"><?php echo $lang['so-prob-acent']; ?></th>
		<td>
		  <select name="mlv_encode" id="mlv_encode">
        	<option <?php if($mlv_options['mlv_encode'] == 'y') { echo 'selected'; } ?> value="y"><?php echo $lang['sim']; ?></option>
			<option <?php if($mlv_options['mlv_encode'] == 'n') { echo 'selected'; } ?> value="n"><?php echo $lang['nao']; ?></option>
		  </select>
		</td>
	 </tr>
	</table>

		  <br/>
		  <br/>
           <div class="submit" style="text-align: left;margin-top:10px">
            <input type="submit" name="mlv_atualizar" value="Atualizar" />
          </div>
      </form>
  </div>
<?php
}

// Actions and Filters
add_action('admin_menu', 'mlv_add_options_page');
add_filter('the_content', 'auto_vc');

/**
 * Adds Foo_Widget widget.
 */
class Mlv_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'mlv_widget', // Base ID
			'MLV Contextual', // Name
			array( 'description' => __( 'Anúncios do MLV Contextual', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		if (is_single()) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			
			$qtd = apply_filters( 'widget_title', $instance['qtd'] );
			if ($qtd == '') $qtd = 2;

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
				
			global $vitrine_ml;
			$vitrine_ml = '';
			widget_contextual($qtd,1);
			
			echo $after_widget;
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Anúncios Relacionados', 'text_domain' );
		}
		
		if ( isset( $instance[ 'qtd' ] ) ) {
			$qtd = $instance[ 'qtd' ];
		}
		else {
			$qtd = __( '2', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Título:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'qtd' ); ?>">Quantidade de anúncios:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'qtd' ); ?>" name="<?php echo $this->get_field_name( 'qtd' ); ?>" type="text" value="<?php echo esc_attr( $qtd ); ?>" />
		</p>
		<?php
	}

} // class Foo_Widget

// register Foo_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "mlv_widget" );' ) );

?>