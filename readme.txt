=== MLV Contextual ===Contributors: Thiago MobilonDonate link: http://tecnoblog.net/
Tags: Mercado Livre, monetização, contextual, vitrineRequires at least: 2.6Tested up to:  2.8.6Stable tag: 2.0b2Exibe uma vitrine do Mercado Livre em HTML, com ofertas contextualizadas com a temática dos posts.== Description ==Nova versão do plugin de Mercado Livre mais utilizado no mundo!Principais novidades:* Suporte nativo a TODOS os países onde o Mercado Livre atua com o PMS;* Exibe o preço parcelado das ofertas;* Exiba a vitrine apenas em posts com mais de 7, 15 ou 30 dias. Seu layout ficará bem clean para seus leitores fiéis, e com publicidade para os visitantes que chegam via sistemas de busca;* Quando nenhuma palavra chave é especificada, o MLV_Contextual automaticamente exibe as ofertas mais buscadas / vendidas do Mercado Livre;* Tradução área de admin para espanhol;* Espaço para colocar um título na Vitrine;* Seleciona automaticamente Fopen ou Curl;* Deleta os custom fields quando o post é deletado. Seu banco de dados fica limpo!* CSS em arquivo separado para deixar a página mais leve (ajuda na indexação);* Correção do Bug onde as imagens não apareciam em alguns hosts;* Correção de outros Bugs pequenos;* Conceito:Este plugin foi desenvolvido pensando em uma forma de monetizar seus leitores (fiéis ou não), e também aproveitar o poder dos sistemas de busca.
* Como funciona?O MLV_Contextual exibe uma vitrine de ofertas do Mercado Livre em HTML com produtos relacionadas ao post. O fato de a vitrine ser em HTML potencializa o rankeamento de seus posts, uma vez que os sistemas de busca indexam não apenas seu conteúdo, mas o texto das ofertas. Como ofertas são relacionadas ao post, os sistemas de buscas entenderão que você está agregando valor ao seu texto.

Por isso irá acontecer de você receber visitas de usuários que estavam procurando por alguma oferta que está sendo exibida em seu post.

A contextualidade faz com que não só usuários que chegam por se interessem pelos produtos, mas também seus leitores. A partir do momento que um deles clicar em um dos anúncios, um cookie do Mercado Livre será descarregado em sua máquina. Se alguém fizer alguma compra desta máquina nos próximos 30 dias, a comissão por esta compra será sua.

Vale frizar que o cookie é descarregado pelo próprio Mercado Livre, por isso está totalmente de acordo com os termos de uso do programa.* É automático?A vitrine pode ser inserida automaticamente acima ou abaixo do post. Você não precisa tocar uma linha de código para começar a ganhar com o Mercado Livre.* Posso Customizar?

O visual da vitrine pode ser editado alterando o arquivo "mlv_stylesheet.css", que se encontra na pasta "mlv_contextual"(necessita entendimento básico de CSS).* É de graça?Sim, a vitrine é gratuita. Você fica com 100% dos lucros gerados por ela (sério, pode olhar no código!).

== Installation ==1. Baixe e descompacte o plugin
2. Envie a pasta "mlv_contextual" para a pasta wp-content/plugins do seu Wordpress
3. Ative no painel de controle== Frequently Asked Questions ==* O que é o ID de afiliado do Mercado Livre?É um número que você deve gerar para trackear os cliques nas ofertas. Para isso faça o login na sua conta do Mercado Livre, entre em ferramentas, e clique em "Link Personalizado".* Depois de ativado e configurado, como faço para as ofertas aparecerem?

Você deve definir alguma palavra chave para o post em questão, através do campo MLV-Contextual, na própria área de edição do post.* O Tecnoblog ganha alguma coisa com o MLV_Contextual?Nadinha. Os lucros são 100% do usuário, mas um link e seu feedback já nos deixaria mais feliz. :-D

== Screenshots ==

Exemplo de vitrine contextualizada:<br/>
http://tecnoblog.net/wp-content/uploads/2009/12/mlv_post.JPG

Campo para inserir palavras chave:<br/>
http://tecnoblog.net/wp-content/uploads/2009/12/mlv_edit.JPGPainel de admin do plugin:<br/>http://tecnoblog.net/wp-content/uploads/2009/12/mlv_admin.JPG

== Histórico ==Versão 2.0b1 - 11 e 12/2009* Nova função para limpar Keywords;<br/>* Auto Fopen/Curl;<br/>* Multipaís;<br/>* Tradução área de admin para espanhol;<br/>* Func. MLV apenas em posts antigos;<br/>* Funções do loop xml voltaram para o arquivo principal;<br/>* Quando nenhuma palavra chave é especificada, o MLV_Contextual automaticamente exibe as ofertas mais buscadas / vendidas do Mercado Livre.<br/>Versão 2.0a - 11/2008* Correção do Bug onde as imagens não apareciam em alguns hosts;<br/>* Inclusão de parcelamento Mercado Pago;<br/>* Deleta os custom fields quando o post é deletado. DB fica limpa;<br/>* Correção do Bug que duplica custom fields;<br/>* CSS em arquivo separado para deixar a página mais leve;<br/>* Alterado o título da vitrine em "Settings";<br/>* Adicionado suporte ao Plugin "Palavras de Monetização";<br/>* Funções do loop xml colocadas em um arquivo separado.<br/>

Versão 1.5 - 21/05/2008

* Adicionado campo para inserção de palavras chave, direto da área de edição de posts do WordPress;<br/>
* Recurso de Comparação de Preços no Buscapé foi descontinuado.<br/>


Versão 1.3.1 - 24/03/2008

* Novo nome. Agora ao invés de ML Vitrine Contextual, o plugin se chama MLV Contextual;<br/>
* O link das imagens agora aponta para uma lista de ofertas. Isto aumentou a conversão significativamente;<br/>
* Adicionado a função Fopen. Agora o usuário pode escolher se quer usar Curl ou Fopen;<br/>
* Adicionado a opção "Random" para o Filtro1 e Ordenação de ofertas;<br/>
* Adicionado um hack, que corrige os bugs com o Parser no Bluehost;<br/>
* Adicionada mensagem de Copyright;<br/>
* Adicionado filtro que remove caracteres inúteis do título das ofertas. Desta forma a vitrine polui menos o blog.<br/>== Copyright ==    Copyright 2007 @ 2008  Thiago Mobilon (mobilon@tecnoblog.net)

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