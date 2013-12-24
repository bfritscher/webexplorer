<?php
$default_html = <<<DEFAULT_HTML_TEMPLATE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <title></title>	
  </head>
  <body>
	
	
  </body>
</html>
DEFAULT_HTML_TEMPLATE;

$default_css = <<<DEFAULT_CSS_TEMPLATE

DEFAULT_CSS_TEMPLATE;

$default_js = <<<DEFAULT_JS_TEMPLATE

DEFAULT_JS_TEMPLATE;

//SAMPLE Other Template
if ($this->data['Webpage']['name'] == 'tp1') {

$default_css = <<<DEFAULT_CSS_TEMPLATE
html, body, h1, p{
    font-family: Helvetica;
    margin: 0;
}

.stopfloat{
    clear: both;
}


table{
    border-collapse:collapse;
}
DEFAULT_CSS_TEMPLATE;

}

?>
<?php echo $html->script('jquery-ui-1.8.4.custom.min', array('inline' => false)); ?>
<?php echo $html->script('jquery.form', array('inline' => false)); ?>
<?php echo $html->script('codemirror/codemirror', array('inline' => false)); ?>
<?php echo $html->script('codemirror/zen_codemirror.min', array('inline' => false)); ?>
<?php echo $html->script('webjslint', array('inline' => false)); ?>
<?php echo $this->Html->css('cupertino/jquery-ui-1.8.4.custom', null, array('inline' => false)); ?>

<?php $html->scriptStart(array('inline' => false));?>
var baseurl = '<?php echo $html->url('/');?>';
var html_editor;
var css_editor;
var js_editor;
var js_tab;

$(document).ready(
	function() {
		html_editor = CodeMirror.fromTextArea('WebpageHtml',
				{parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"],
				 stylesheet: [baseurl +"css/codemirror/xmlcolors.css", baseurl +"css/codemirror/jscolors.css", baseurl +"css/codemirror/csscolors.css"],
				 path: baseurl + "js/codemirror/",
				 textWrapping: false,
				 minHeight: 240,
				 height: '100%',
				 lineNumbers: true,
				 onChange: updatePreview,
                // add Zen Coding support
                syntax: 'html',
                onLoad: function(editor) {
                    zen_editor.bind(editor);
                },
                    saveFunction: function(){$('form').submit();}
                 });
		css_editor = CodeMirror.fromTextArea('WebpageCss',
				{parserfile: ["parsecss.js"],
				 stylesheet: [baseurl +"css/codemirror/csscolors.css"],
				 path: baseurl + "js/codemirror/",
				 textWrapping: false,
				 minHeight: 240,
				 height: '100%',
				 lineNumbers: true,
				 onChange: updatePreview,
				 indentUnit: 4,
                 saveFunction: function(){$('form').submit();}
                 });
		js_editor = CodeMirror.fromTextArea('WebpageJs',
				{parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
				 stylesheet: [ baseurl +"css/codemirror/jscolors.css"],
				 path: baseurl + "js/codemirror/",
				 textWrapping: false,
				 minHeight: 240,
				 height: '100%',
				 lineNumbers: true,
				 indentUnit: 4,
                 saveFunction: function(){$('form').submit();},
				 onChange: function(){
					js_tab.text('JavaScript*');
				 }});
		
		js_tab = $('a[href=#tab-js]');
		
		$(".tab_content").hide(); //Hide all content
		$("ul.tabs li:first").addClass("active").show(); //Activate first tab
		$(".tab_content:first").show(); //Show first tab content
		$("#tabs li").click(function() {
			switchTab($(this));
			return false;
		});
		
		
		$( "#window-top" ).resizable({
			start: function(){
				// fix for IE and FFox (to stop resizing issues with iFrames see http://dev.jqueryui.com/ticket/3176)
				$('iframe:visible').each(function(){
					var d = $("<div class=\"temp_div\"><\/div>"),
					ifr = $(this);
					ifr.parent().append(d[0]);
					d.css({position:'absolute'});
					d.css({top: ifr.position().top, left:0});
					d.height(ifr.height());
					d.width('100%');
				});
			},
			resize: function(event, ui){
				$('.tab_container, .ui-resizable-e').css('height', ui.size.height - 42);
			},
			stop: function(){
				// fix for IE and FFox (to stop resizing issues with iFrames see http://dev.jqueryui.com/ticket/3176)
				$('.temp_div').remove();
				$( "#window-top" ).width('100%');
			},
			handles: 's',
			minHeight: 300 
		});
				
		setTimeout(updatePreview, 1000);
		
		$('#tab-preview-link').click(function(){
			updatePreview();
		});
		
<?php if($preview_mode == 'preview-bottom'):?>
		$('#livepreview-bottom').resizable({
			start: function(){
				// fix for IE and FFox (to stop resizing issues with iFrames see http://dev.jqueryui.com/ticket/3176)
				ifr = $('#livepreview-bottom iframe');
				var d = $("<div><\/div>");

				$('#livepreview-bottom').append(d[0]);
				d[0].id = 'temp_div';
				d.css({position:'absolute'});
				d.css({top: ifr.position().top, left:0});
				d.height(ifr.height());
				d.width('100%');
			},
			stop: function(){
				// fix for IE and FFox (to stop resizing issues with iFrames see http://dev.jqueryui.com/ticket/3176)
				$('#temp_div').remove();
				$('#livepreview-bottom').width('100%');
			},
			handles: 's',
			minHeight: 300 
		});
<?php endif; ?>
		
<?php if($preview_mode == 'preview-right'):?>		
		$('#window-main').resizable({
			start: function(){
				// fix for IE and FFox (to stop resizing issues with iFrames see http://dev.jqueryui.com/ticket/3176)
				ifr = $('#window-main iframe:visible');
				var d = $("<div><\/div>");

				$('#window-main').append(d[0]);
				d[0].id = 'temp_div';
				d.css({position:'absolute'});
				d.css({top: ifr.position().top, left:0});
				d.height(ifr.height());
				d.width('100%');
				
				ifr = $('#window-right iframe:visible');
				d = $("<div><\/div>");
				
				$('#window-right').append(d[0]);
				d[0].id = 'temp_div2';
				d.css({position:'absolute'});
				d.css({top: ifr.position().top, left:0});
				d.height(ifr.height());
				d.width('100%');
				
			},
			resize: function(event, ui){
				$('#window-right').css('marginLeft', ui.size.width + 10);
			},
			stop: function(){
				// fix for IE and FFox (to stop resizing issues with iFrames see http://dev.jqueryui.com/ticket/3176)
				$('#temp_div').remove();
				$('#temp_div2').remove();
			},
			handles: 'e'
		});
<?php endif;?>
		
		$('#jslint-check').click(function(){
			var result = JSLINT(js_editor.getCode(), {devel: true, bitwise: true, undef: true, browser: true, unparam: true, sloppy: true, eqeq: true, sub: true, vars: true, white: true, css: true, cap: true, plusplus: true, on: true, regexp: true, fragment: true, maxerr: 50, indent: 4, predef: ['$']})
			html_result = 'no errors!',
			report = $('#jslint-report');
			if(!result){
				report.empty();
				$.each(JSLINT.errors, function(index, error){
                    if(error && error.evidence){
                        $('#jslint-report').append("<p class=\"evidence\">" + error.evidence + "<\/p>");
                        var p = $("<p> " + error.reason + "<\/p>").appendTo('#jslint-report')
                        $("<a href=\"#\">Problem at line " + error.line + " character " + error.character + "<\/a>").prependTo(p)
                        .click(function(){
                            jsSelectError(error.line, error.character, error.a.length);
                            return false;
                        });
                    }
				});
				report.effect("highlight", { 
        			color: "red" 
    				}, 1000); 
			}else{
				report.html(html_result)
				.effect("highlight", { 
        			color: "green" 
    				}, 1000);
				setTimeout(function(){report.slideUp()}, 2000);
			}
			$('#jslint-report')
			switchTab($('a[href=#tab-js]').parent());
		});	
		
		var selectedTab = getCookie('webpageeditor_selectedtab');
		if(selectedTab){
			switchTab($('a[href=' + selectedTab + ']').parent());
		}
		
		$('select.menu').change(function(){
			pageid = $(this).val();
			if(pageid != 'none'){
				window.location = '<?php echo $html->url((isset($this->params['prefix']) ? $this->params['prefix'] . '/' : '') . '/webexplorer/edit/'); ?>' + pageid;
			}
		});  
		
	}
);

function setCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function jsSelectError(line, from, length){
	var handler = js_editor.nthLine(line);
	js_editor.selectLines(handler, from-1, handler, from + length - 1);
}

function switchTab(tab){
	$("ul.tabs li").removeClass("active"); //Remove any "active" class
	tab.addClass("active"); //Add "active" class to selected tab
	$(".tab_content").hide(); //Hide all tab content
	var activeTab = tab.find("a").attr("href"); //Find the href attribute value to identify the active tab + content
	$(activeTab).show(); //Fade in the active ID content
	setCookie('webpageeditor_selectedtab', activeTab, 1);
}

function updatePreview(){
	var doc = $('#livepreview')[0], 
    win = doc.contentDocument || doc.contentWindow.document,
    source = html_editor.getCode(),
	headend = source.indexOf("<\/head"),
	html_src = source.substr(0, headend);
	html_src += "<style type=\"text/css\">\n<!-- ";
	html_src += css_editor.getCode();
	html_src += " --><\/style>\n";
	html_src += "<script type=\"text/javascript\">\n";
	html_src += "\/\/<![CDATA[\n";
	html_src += js_editor.getCode();
	html_src += "\/\/]]>\n";
	html_src += "<\/script>\n";
	html_src += source.substr(headend);
	win.open();
	win.write(html_src);
	win.close();
	if($('#autosave:checked').length){
		html_editor.save();
		css_editor.save();
		js_editor.save()
		$('form').ajaxSubmit({error: function (jqXHR, textStatus, errorThrown){
            //force post to have a relogin; could do ajaxlogin...
            $('form').submit();
        }}); 
		js_tab.text('JavaScript');
	}
}

<?php $html->scriptEnd();?>
<div id="rightmenu">
    <?php echo $this->Html->link('logout', array('admin'=>false, 'controller'=>'users', 'action'=>'logout'));?> - 
	<?php echo isset($this->data['User']['full_name']) ? $this->data['User']['full_name'] : '-'; ?>
	<?php 	
		if($isSaveEnabled){
			echo $this->Form->select('changepage', $webpages, $this->data['Webpage']['name'], array('class'=>'menu', 'empty'=>'..'));
		}
		else if($isReviewEnabled){
			echo $this->data['Webpage']['created'] . " " . $this->Html->link('retour', array('action' => 'rendu'));
		}else {
			echo $this->Html->link('retour', array('action' => 'index'));
		}
	?>
</div>
<h1><a href="<?php echo $this->Html->url(array('controller'=>'webexplorer', 'action'=>'index'));?>"><span>Web</span> Explorer</a><span> : </span><span><?php echo $this->data['Webpage']['name'];?></span></h1>

<?php

if(isset($this->data['WebpageTp']) && !$isReviewEnabled){	
?>
<div>
<strong>Rendu:</strong> <?php echo $this->data['Webpage']['created'];?><br />
<strong>Point:</strong> <?php echo is_null($this->data['WebpageTp']['point']) ? 'pas corrigé' : $this->data['WebpageTp']['point'];?><br />
<strong>Commentaire:</strong> <?php echo nl2br($this->data['WebpageTp']['comment']);?><br />
<strong>Evaluateur:</strong> <?php echo $this->data['Evaluator']['full_name'];?>
</div>
<?php
}elseif(isset($this->data['WebpageTp']) && $isReviewEnabled){
	echo $this->Form->create('WebpageTp', array('url' => array('controller'=>'webexplorer'),'action'=>'eval_save'));
	echo $this->Form->input('id');
	echo $this->Form->input('name', array('type'=>'hidden'));
	if(is_null($this->data['WebpageTp']['point'])){
		echo "pas encore corrigé";
		$this->data['WebpageTp']['point'] = 1;
	}
	echo $this->Form->input('point', array('type' => 'checkbox'));
	echo $this->Form->input('comment', array('div' => false, 'label' => false, 'title' => $this->data['Evaluator']['full_name']));
    echo $this->Form->submit('enregistrer');
	echo $this->Form->end();
}
?>

<?php 
	echo $this->Form->create('Webpage', array('url' => array('controller'=>'webexplorer'),'action'=>'save/' . $this->data['Webpage']['name']));
	echo $this->Form->input('id');
	echo $this->Form->input('name', array('type'=>'hidden'));
?>	
<div id="menu">
<?php 
if($isSaveEnabled){
	echo $this->Form->submit('webpageeditor/disk-black.png', array( 'div'=>false));
?>
	<input type="checkbox" id="autosave" name="autosave" title="Enregistrement automatique" value="on" checked="checked"/>
<?php
	echo $this->Html->image('webpageeditor/ui-separator.png');
	echo $this->Html->link($this->Html->image('webpageeditor/validation-document.png'),
"http://validator.w3.org/unicorn/check?ucn_uri=" . 
urlencode($this->Html->url('/webexplorer/view/' . 
$this->data['Webpage']['id'], true)) . "&tests=markup-validator&tests=css-validator&ucn_lang=fr&ucn_task=custom&warning=2&profile=css3&usermedium=screen",
 array('target'=>'_blank', 'escape'=>false, 'title'=>'validate'));
}
?>
<?php echo $this->Html->link($this->Html->image('webpageeditor/gear--exclamation.png'), '#', array('escape'=>false, 'title'=>'check with jslint', 'id'=>'jslint-check'));?>
<?php echo $this->Html->image('webpageeditor/ui-separator.png');?>
<?php echo $this->Html->link($this->Html->image('webpageeditor/application.png'), array($this->data['Webpage']['name'], 'layout:single'), array('escape'=>false, 'title'=>'single', 'class'=> $preview_mode == 'preview-single' ? 'active' : null));?>
<?php echo $this->Html->link($this->Html->image('webpageeditor/application-split.png'), array($this->data['Webpage']['name'], 'layout:right'), array('escape'=>false, 'title'=>'right', 'class'=> $preview_mode == 'preview-right' ? 'active' : null));?>
<?php echo $this->Html->link($this->Html->image('webpageeditor/application-split-vertical.png'), array($this->data['Webpage']['name'], 'layout:bottom'), array('escape'=>false, 'title'=>'bottom', 'class'=> $preview_mode == 'preview-bottom' ? 'active' : null));?>
<?php echo $this->Html->image('webpageeditor/ui-separator.png');?>
<?php
if($isReviewEnabled){
   echo $this->Html->link($this->Html->image('webpageeditor/application-browser.png'), array('action'=>'viewtp', $this->data['WebpageTp']['id']), array('target'=>'_blank', 'escape'=>false, 'title'=>'preview in new window'));
}else{
    echo $this->Html->link($this->Html->image('webpageeditor/application-browser.png'), array('action'=>'view', $this->data['Webpage']['name']), array('target'=>'_blank', 'escape'=>false, 'title'=>'preview in new window'));
    
}?>

<?php
if($isTpSubmitEnabled){
	echo $this->Html->image('webpageeditor/ui-separator.png');
	echo $this->Form->submit('rendre tp', array( 'div'=>false, 'name'=>'data[savetp]'));
}
?>
<br style="clear:both;"/>
</div>

<div id="window-top">
	<div id="window-main" style="width:<?php echo $preview_mode == 'preview-right' ? '700px' : '100%'; ?>">
		<ul id="tabs" class="tabs">
			<li><a href="#tab-html">HTML</a></li>
			<li><a href="#tab-css">CSS</a></li>
			<li><a href="#tab-js">JavaScript</a></li>
<?php if($preview_mode == 'preview-single'):?>	
			<li><a href="#tab-preview" id="tab-preview-link">Preview</a></li>
<?php endif;?>
		</ul>
		<div class="tab_container">
			<?php echo $this->Form->input('html', array( 'label' => false, 'div' => array('id'=>'tab-html', 'class'=>'tab_content'), 'default'=>$default_html));?>
			<?php echo $this->Form->input('css',array( 'label' => false, 'div' => array('id'=>'tab-css', 'class'=>'tab_content'), 'default'=>$default_css));?>
			<?php echo $this->Form->input('js',array( 'label' => false, 'div' => array('id'=>'tab-js',  'class'=>'tab_content'), 'default'=>$default_js,
			'before'=>'<div id="jslint-report"></div>'));?>
<?php if($preview_mode == 'preview-single'):?>	
			<div id="tab-preview" class="tab_content">
				<iframe id="livepreview" frameborder="no"></iframe>
			</div>
<?php endif;?>			
		</div>
	</div>
<?php if($preview_mode == 'preview-right'):?>	
	<div  id="window-right" style="margin-left: 710px;">
		<ul class="tabs">
			<li><a href="#tab-preview" class="active" id="tab-preview-link">Preview</a></li>
		</ul>
		<div class="tab_container"><iframe id="livepreview" frameborder="no"></iframe></div>
	</div>
<?php endif;?>	
	<div style="clear:both;"></div>
</div>
<?php echo $this->Form->end();?>

<?php if($preview_mode == 'preview-bottom'):?>
	<div id="livepreview-bottom">
		<ul class="tabs">
			<li><a href="#tab-preview" class="active" id="tab-preview-link">Preview</a></li>
		</ul>
		<iframe id="livepreview" frameborder="no"></iframe></div>
<?php endif;?>