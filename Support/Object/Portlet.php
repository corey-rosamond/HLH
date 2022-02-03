<?php
/**
 * Portlet
 *
 * @package Framework\Support\Object
 * @version 1.0.0
 */
namespace Framework\Support\Object;

/**
 * Portlet
 *
 * Portelts are small page containers very portable and highly configurable
 * They are here to make creating a nice looking page simple
 */

\Framework\_IncludeCorrect(FRAMEWORK_ROOT."Support".DSEP."Abstracts".DSEP."ExceptionSafe.php");
class Portlet extends \Framework\Support\Abstracts\ExceptionSafe
{
	private $buffer = null;

	private $title;

	private $icon;

	private $fontColor;

  private $backgroundColor;

	private $actions = [];

  private $tools = [];

  private $type;


	public function __construct( array $paramaters = array() )
  {
    $this->configure( $paramaters );
		return $this;
	}

  private function configure( $paramaters )
  {
    $defaults = [
      "title"          	=>  "protlet title",
      "icon"           	=>  "fa fa-users",
      "fontColor"      	=>  "blue-steel",
      "backgroundColor"	=>  "blue-steel",
      "type"           	=>  false,
      "portletStyles"  	=>  false,
      "bodyStyles"     	=>  false
    ];
    foreach( $defaults as $key => $value ){
      if( isset( $paramaters[$key] ) ){
        $this->{$key} = $paramaters[$key];
        continue;
      }
      $this->{$key} = $value;
    }
  }

	public function render()
  {
		return "{$this->start()} {$this->header()} {$this->body()} {$this->content()} {$this->end()}";
	}

	private function start()
	{
    return  "<div class='portlet {$this->type} {$this->fontColor}' style='{$this->portletStyles}'>";
	}

	private function header()
	{
		return "
		<div class='portlet-title'>
		  <div class='caption'>
			 <i class='{$this->icon} {$this->fontColor}'>&nbsp</i>
			 <span class='caption-subject {$this->fontColor}' style='font-weight:normal;font-size:16px;line-height:37px;'>{$this->title}</span>
		  </div>{$this->tool()}{$this->action()}
		</div>";
	}

	private function body()
	{
		return "<div class=\"portlet-body\" style='{$this->bodyStyles}'>";
	}

	private function content()
  {
		return $this->buffer;
	}

	private function end()
	{
		return "</div></div>";
	}

	private function tool()
	{
		if( !$this->tools ){
      return '';
    }
		$html = '<div class="tools">';
		foreach( $this->tools as $tool => $data ){
			switch( $tool ){
				case 'collapse':
					$html .= '<a href="javascript:;" class="collapse"> </a>&nbsp;&nbsp;';
				break;
				case 'fullscreen':
					$html .= '<a href="javascript:;" class="fullscreen"> </a>&nbsp;&nbsp;';
				break;
				case 'config':
					$html .= '<a href="javascript:;" class="config"> </a>&nbsp;&nbsp;';
				break;
				case 'removed':
					$html .= '<a href="javascript:;" class="remove"> </a>&nbsp;&nbsp;';
				break;
				case 'button':
					if( !isset( $data['text'] )){
            $data['text'] = 'Button Text';
          }
					if( !isset( $data['button-class'] )){
            $data['button-class'] = 'btn btn-default';
          }
					if( !isset( $data['icon'] )){
            $data['icon'] = 'fa fa-pencil';
          }
					$html .= '<a class="'.$data['button-class'].' btn-sm" href="javascript:;"><i class="'.$data['icon'].'"></i> '.$data['text'].' </a>';
				break;
				case 'ajax':
					$html .= '<a href="javascript:;" class="reload" data-url="/Request/Portlet'.$data['data-url'].'" data-error-display="notific8" data-load="true" > </a>';
				break;
			}
		}
		return "$html</div>";
	}

	private function action()
	{
		if( !$this->actions ){
      return null;
    }
		$html = '<div class="actions">';
		foreach( $this->actions as $action ){
			switch( $action['name'] ){
				case 'button':
					if( !isset( $action['text'] )){
            $action['text'] = 'Button Text';
          }
					if( !isset( $action['button-class'] )){
            $action['button-class'] = 'btn btn-default';
          }
					if( !isset( $action['icon'] )){
            $action['icon'] = 'fa fa-pencil';
          }
					$html .= '<a class="'.$action['button-class'].' btn-sm" href="javascript:;"><i class="'.$action['icon'].'"></i> '.$action['text'].' </a>';
				break;
			}
		}
		return $html.'</div>';
	}


	public function actions( $actions = false )
	{
    if( !$actions ){
      return $this->actions;
    }
		$this->actions = $actions;
		return $this;
	}

	public function tools( $tools = false )
	{
    if( !$tools ){
      return $this->tools;
    }
		$this->tools = $tools;
		return $this;
	}

  public function backgroundColor( $color = false )
  {
    if( !$color ){
      return $this->backgroundColor;
    }
    $this->backgroundColor = $color;
    return $this;
  }

  public function fontColor( $color = false )
  {
    if( !$color ){
      return $this->fontColor;
    }
    $this->fontColor = $color;
    return $this;
  }

  public function icon( $icon = false )
  {
    if( !$icon ){
      return $this->icon;
    }
    $this->icon = $icon;
    return $this;
  }

  public function type( $type = false )
  {
    if( !$type ){
      return $this->type;
    }
    $this->type = $type;
    return $this;
  }

  public function buffer( $content = false )
  {
    if( !$content ){
      return $this->buffer;
    }
    $this->buffer .= $content;
    return $this;
  }

	public function title( $content = false )
	{
		if( !$content ){
			return $this->title;
		}
		return $this->title = $content;

	}
}
