<?php
//var_dump('some');
define('THEMEPATH',__DIR__.'/src/');
global $script_name;
$script_name=$_SERVER['SCRIPT_NAME'];
if($script_name=='/src/index.php'){
	$files=glob(__DIR__."/src/*.php");
	$subtemplates=[];
	echo '<h2>Страницы</h2>';
	
	echo '<ol>';
	foreach($files as $file){
		$url=str_replace(__DIR__.'/src/','',$file);
		$url=str_replace('.php','.html',$url);
		//var_dump($url);
		if($url=='index.html')continue;
		if(substr($url,0,4)=='sub-'){
			$subtemplates[]=$url;
			continue;
		}
		$names=['front-page.html'=>'Главная страница'];
		$name=$url;
		if(!empty($names[$url])){
			$name=$names[$url];
		}
		
		echo '<li><a href="/'.$url.'">'.$name.'</a></li>';
	}
	echo '</ol>';
	if(!empty($subtemplates)){
		echo '<h2>субшаблоны</h2>';
		echo '<ol>';
		foreach($subtemplates as $tpl){
			echo '<li>'.$tpl.'</li>';
		}
		echo '</ol>';
	}
	
	
	//echo 'КОгда-нибудь тут будет список файлов.';
	return;
};
include_once(__DIR__.'/data.php');
class WeblamasTemplate{
	static function get_subtemplates(){
		return array();
	}
	static function loadTemplate($templates){
		global $script_name;
		$s=file_get_contents(__DIR__.$script_name);
		ob_start();
		include(__DIR__.$script_name);
		$q=ob_get_clean();
	if(substr($s,0,11)=='extend sub-'){
			$s=explode("\n",$s);
			$s=$s[0];
			$file=trim(str_replace('extend ','',$s)).'.php';
			ob_start();
			include(__DIR__.'/src/'.$file);
			$l=ob_get_clean();
			$q=str_replace('%content%',$q,$l);
			$q=str_replace($s."\n",'',$q);
		}
		echo $q;
	}
}
function parse_atts( $text ) {
		$atts = array( 'options' => array(), 'values' => array() );
		$text = preg_replace( "/[\x{00a0}\x{200b}]+/u", " ", $text );
		$text = trim( $text );

		$pattern = '%^([-+*=0-9a-zA-Z:.!?#$&@_/|\%\r\n\t ]*?)((?:[\r\n\t ]*"[^"]*"|[\r\n\t ]*\'[^\']*\')*)$%';

		if ( preg_match( $pattern, $text, $match ) ) {
			if ( ! empty( $match[1] ) ) {
				$atts['options'] = preg_split( '/[\r\n\t ]+/', trim( $match[1] ) );
			}

			if ( ! empty( $match[2] ) ) {
				preg_match_all( '/"[^"]*"|\'[^\']*\'/', $match[2], $matched_values );
				$atts['values'] =  $matched_values[0] ;
			}
		} else {
			$atts = $text;
		}

		return $atts;
}
function get_shortcode_regex( $tagnames = null ) {
    global $shortcode_tags;
 
    if ( empty( $tagnames ) ) {
        $tagnames = array_keys( $shortcode_tags );
    }
    $tagregexp = implode( '|', array_map( 'preg_quote', $tagnames ) );
 
    // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag().
    // Also, see shortcode_unautop() and shortcode.js.
 
    // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
    return '\\['                             // Opening bracket.
        . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
        . "($tagregexp)"                     // 2: Shortcode name.
        . '(?![\\w-])'                       // Not followed by word character or hyphen.
        . '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
        .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
        .     '(?:'
        .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
        .         '[^\\]\\/]*'               // Not a closing bracket or forward slash.
        .     ')*?'
        . ')'
        . '(?:'
        .     '(\\/)'                        // 4: Self closing tag...
        .     '\\]'                          // ...and closing bracket.
        . '|'
        .     '\\]'                          // Closing bracket.
        .     '(?:'
        .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
        .             '[^\\[]*+'             // Not an opening bracket.
        .             '(?:'
        .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
        .                 '[^\\[]*+'         // Not an opening bracket.
        .             ')*+'
        .         ')'
        .         '\\[\\/\\2\\]'             // Closing shortcode tag.
        .     ')?'
        . ')'
        . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]].
    // phpcs:enable
}
function get_shortcode_atts_regex() {
    return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';
}
function shortcode_parse_atts( $text ) {
    $atts    = array();
    $pattern = get_shortcode_atts_regex();
    $text    = preg_replace( "/[\x{00a0}\x{200b}]+/u", ' ', $text );
    if ( preg_match_all( $pattern, $text, $match, PREG_SET_ORDER ) ) {
        foreach ( $match as $m ) {
            if ( ! empty( $m[1] ) ) {
                $atts[ strtolower( $m[1] ) ] = stripcslashes( $m[2] );
            } elseif ( ! empty( $m[3] ) ) {
                $atts[ strtolower( $m[3] ) ] = stripcslashes( $m[4] );
            } elseif ( ! empty( $m[5] ) ) {
                $atts[ strtolower( $m[5] ) ] = stripcslashes( $m[6] );
            } elseif ( isset( $m[7] ) && strlen( $m[7] ) ) {
                $atts[] = stripcslashes( $m[7] );
            } elseif ( isset( $m[8] ) && strlen( $m[8] ) ) {
                $atts[] = stripcslashes( $m[8] );
            } elseif ( isset( $m[9] ) ) {
                $atts[] = stripcslashes( $m[9] );
            }
        }
 
        // Reject any unclosed HTML elements.
        foreach ( $atts as &$value ) {
            if ( false !== strpos( $value, '<' ) ) {
                if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
                    $value = '';
                }
            }
        }
    } else {
        $atts = ltrim( $text );
    }
 
    return $atts;
}
function render_field($tag,$attrs,$inside){
	$s='<span class="wpcf7-form-control-wrap';
	if($tag=='wlsubmit'){
		$s.=' wpcf7-form-control-wrap_button';
	}elseif(in_array($tag,['filewl','wlfile'])){
		$s.=' form__file';
	}
	$s.='">';
	foreach($attrs['values'] as $k=>$v){
		$attrs['values'][$k]=substr($v,1,-1);
	}
	if(substr($tag,-1)=='*'){
		$tag=substr($tag,0,-1);
	}
	if(in_array($tag,['text','email','tel'])){
		$name=array_shift($attrs['options']);
		$s.='<input type="'.$tag.'" name="'.$name.'" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" placeholder="'.$attrs['values'][0].'">';
	}elseif($tag=='textarea'){
		$name=array_shift($attrs['options']);
		$s.='<textarea name="'.$name.'" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea wpcf7-validates-as-required" aria-required="false" aria-invalid="false" placeholder="'.$attrs['values'][0].'"></textarea>';
	}elseif($tag=='radio'){
		$s.='<span class="wpcf7-form-control wpcf7-radio">';
		foreach($attrs['values'] as $v){
			$s.='<span class="wpcf7-list-item first"><label><input type="radio" name="radio-228" value="January — April 2022 (Sign up now)" checked="checked" /><span class="wpcf7-list-item-label">'.$v.'</span></label></span>';
		}
		$s.='</span>';
	}elseif($tag=='select'){
		$s.='<select name="select" class="wpcf7-form-control wpcf7-select" aria-invalid="false">';
		foreach($attrs['values'] as $v){
			$s.='<option value="'.$v.'">'.$v.'</option>';
		}
		$s.='</select>';
	}elseif(in_array($tag,['filewl','wlfile'])){
		$s.='<input type="file" name="files-4[]" size="40" class="wpcf7-form-control wpcf7-multifile"><span>'.$inside.'</span>';
	}elseif($tag=='submitwl'){
		$classes=[];
		foreach($attrs['options'] as $option){
			if(substr($option,0,6)=='class:'){
				$classes[]=substr($option,6);
			}
		}
		$s.='<button class="'.implode(' ',$classes).'">'.$attrs['values'][0].'</button>';
	}elseif($tag=='acceptance'){
		$s.='
	<span class="wpcf7-form-control wpcf7-acceptance">
		<span class="wpcf7-list-item">
			<label>
				<input type="checkbox" name="acceptance-97" value="1" aria-invalid="false" />
				<span class="wpcf7-list-item-label">'.$inside.'</span>
			</label>
		</span>
	</span>';
	}
	$s.='</span>';
	return $s;
}
function render_form($attr){
	$formid=$attr['id'];
	$file='src/template/form_'.$formid.'.php';
	if(!file_exists($file)){
		$file='src/template/form_'.$formid.'.html';
	}
	$text=file_get_contents($file);
	$tagnames = ['textarea','text','text*','submitwl','acceptance','radio','select','wlfile','filewl','tel','tel*'];
	$tagregexp = implode( '|', array_map( 'preg_quote', $tagnames ) );
	$pattern='(\[?)'
		. '\[(' . $tagregexp . ')(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\]'
		. '(?:([^[]*?)\[\/\2\])?'
		. '(\]?)';
	//$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	
	if ( preg_match_all('/'.$pattern.'/s', $text, $match, PREG_SET_ORDER) ){
		foreach($match as $input){
			$replace=$input[0];
			$tag=$input[2];
			$attrs=(parse_atts($input[3]));
			$s=render_field($tag,$attrs,$input[5]);
			$text=str_replace($replace,$s,$text);
		}
	}
	return '<div class="wpcf7"><form class="wpcf7-form '.(!empty($attr['html_class'])?$attr['html_class']:'').'">'.$text.'</form></div>';
}
function do_shortcode($some){
	$pattern=get_shortcode_regex(['contact-form-7']);
	
	preg_match_all("/$pattern/",$some,$matches);
	foreach($matches[0] as $k=>$match){
		$tag  = $matches[2][$k];
		$attr = shortcode_parse_atts( $matches[3][$k] );
		if($tag=='contact-form-7'){
			return render_form($attr);
		}
		var_Dump($attr);
		var_dump($tag);
	}
	
	//var_dump($matches);
	if(!empty($matches['formid'])){
		return render_form($matches['formid']);
	}
	return '';
}
function wp_footer(){};
function wp_head(){
	?>
		<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/main.css"> 
    <title>Верстка</title>
    <script src="/js/jquery-3.6.0.min.js"></script>
	<script src="/js/main.js"></script>
	<?php
}
function wp_nav_menu($arr){
	$arr=array_merge(array('container_class'=>'navbar','container'=>'nav','menu_class'=>'menu'),$arr);
	include('src/template/'.$arr['theme_location'].'.html');
}

class Wobject{
	public function __call($name,$arguments){
		return $this;
	}
	public static function __callStatic($name,$arguments){
		$s=get_called_class();
		return new $s;
	}
	function get(){
		$objects=Config::get('objects');
		return $objects[strtolower(get_called_class())];
	}
}
class WeblamasOptions{
	public static function getValue($key){
		$options=Config::get('options');
		return $options[$key];
	}
	public static function formatValue($key,$format){
		return self::getValue($key);
	}
}
include(__DIR__.'/src/index.php');
