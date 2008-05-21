=== MLV Contextual ===Contributors: Thiago MobilonDonate link: http://tecnoblog.net/archives/plugin-mercado-livre-vitrine-contextual-para-wordpress.php
Tags: Mercado Livre, monetização, contextual, vitrineRequires at least: 2.0.2Tested up to: 2.3.3Stable tag: 1.3.1O plugin exibe uma vitrine de ofertas contextuais com anúncios do Mercado Livre em HTML.== Description ==Este plugin foi desenvolvido pensando em uma forma de monetizar leitores fiéis, e também aproveitar o poder dos sistemas de busca.

Ele exibe uma vitrine de ofertas em HTML relacionadas ao post. Por a vitrine ser em HTML, os sistemas de busca indexarão o texto das ofertas. Junte isto com o fato de que estas ofertas são relacionadas ao post, e os sistemas de buscas entenderão que você está agregando valor ao seu texto.

Por isso irá acontecer de você receber visitas de usuários que estavam procurando por alguma oferta que está sendo exibida em seu post.

A contextualidade faz com que não só os pára-quedistas se interessem pelos produtos, mas também seus leitores. A partir do momento que um deles clicar em um dos anúncios, um cookie do Mercado Livre será descarregado em sua máquina. Se alguém fizer alguma compra desta máquina nos próximos 30 dias, a comissão por esta compra será sua.

Vale frizar que o cookie é descarregado pelo próprio Mercado Livre, assim, está totalmente de acordo com os termos de uso do programa.

== Installation ==1. Baixe e descompacte o plugin
2. Envie o arquivo mlv_contextual.php para a pasta wp-content/plugins do seu Wordpress
3. Ative no painel de controle== Frequently Asked Questions === O que é o ID de afiliado do Mercado Livre?É um número que você deve gerar para trackear os cliques nas ofertas. Para isso faça o login na sua conta do Mercado Livre, entre em ferramentas, e clique em "Link Personalizado".= Depois de ativado e configurado, como faço para as ofertas aparecerem?

Você deve definir alguma palavra chave para o post em questão. Para isso, crie um Custom Field com a chave mlv_word, e coloque a palavra chave relacionada ao post. Os valores mlv_id e mlv_minpr também podem ser usados. O primeiro com um ID de categoria, e o segundo com um preço mínimo.

== Screenshots ==

http://tecnoblog.net/wp-content/uploads/2008/03/mlv_n73.jpg

== Histórico ==

Versão 1.3.1 - 24/03/2008
* Novo nome. Agora ao invés de ML Vitrine Contextual, o plugin se chama MLV Contextual.
* O link das imagens agora aponta para uma lista de ofertas. Isto aumentou a conversão significativamente.
* Adicionado a função Fopen. Agora o usuário pode escolher se quer usar Curl ou Fopen.
* Adicionado a opção "Random" para o Filtro1 e Ordenação de ofertas.
* Adicionado um hack, que corrige os bugs com o Parser no Bluehost.
* Adicionada mensagem de Copyright
* Adicionado filtro que remove caracteres inúteis do título das ofertas. Desta forma a vitrine polui menos o blog.== Copyright ==    Copyright 2007 @ 2008  Thiago Mobilon (contato@tecnoblog.net)

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