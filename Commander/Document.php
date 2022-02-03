<?php

namespace Framework\Commander;

class Document extends \Framework\Support\Abstracts\Singleton
{
  public static function construct()
  { return self::getInstance(); }

  public static function close()
  {
    $instance = self::getInstance();
    return $instance->_fRender().$instance->_eRender().$instance->_pRender();
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                             FUNCTIONS                                               */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_fCollection = [];

  public function addFunction( $code )
  { array_push( $this->_eCollection, $code ); }

  private function _fRender()
  { return "<script language=\"javascript\" type=\"text/javascript\">\n$(document).ready(function(){\n {$this->_fFunctions()} });</script>"; }

  private function _fFunctions()
  {
    $buffer = "";
    foreach( $this->_fCollection as $function ){ $buffer .= "{$function}"; }
    return $buffer;
  }


  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                             EVENTS                                                  */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_eCollection = [];

  public function addEvent( $target, $type, $code )
  { array_push( $this->_eCollection, [ 'target' => $target, 'type' => $type, 'code' => $code ] ); }

  private function _eRender()
  { return "<script language=\"javascript\" type=\"text/javascript\">\n$(document).ready(function(){\n {$this->_eEvents()} });</script>"; }

  private function _eEvents()
  {
    $buffer = "";
    foreach( $this->_eCollection as $event ){
      $buffer .= "$(\"#{$event['target']}\").on(\"{$event['type']}\",function(event){ {$event['code']} });\n";
    }
    return $buffer;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                             POPUP                                                   */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  private $_pCollection = [];

  public function makePopupForm( $triggerId, $popupId, $saveId, $saveCode, $title, $content, $icon )
  {
    $this->addEvent( $triggerId, 'click', "$(\"#{$popupId}\").modal('show').css({'top':'20%'});\n" );
    $this->addEvent( $saveId, "save", $saveCode );
    array_push(
      $this->_pCollection,
      "<div id=\"{$popupId}\" class=\"modal fade\" tabindex=\"-1\" data-width=\"760\">
        {$this->_pHeader($title, $icon)}
        <div class=\"modal-body\">{$content}</div>
        <div class=\"modal-footer bg-grey-steel\">
          <button type=\"button\" data-dismiss=\"modal\" class=\"btn btn-default btn-outline dark border-blue-dark\">
            <i class=\"fa fa-times fa-lg\" style=\"margin-top:-1px;\"></i> Close
          </button>
          <button type=\"button\" id=\"{$saveId}\" class=\"btn btn-default green bold border-blue-dark save-model\">
            <i class=\"fa fa-check fa-lg\" style=\"margin-top:-1px;\"></i> Save changes
          </button>
        </div>
      </div>"
    );
  }

  private function _pHeader( $title, $icon )
  {
    return "
    <div class=\"modal-header bg-grey-steel\">
      <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">
        <span class=\"fa-stack fa-lg\">
          <i class=\"fa fa-square-o fa-stack-2x font-blue-ebonyclay\"></i>
          <i class=\"fa fa-times fa-stack-1x font-blue-ebonyclay\" style=\"margin-top:-2px;margin-left:0px;\"></i>
        </span>
      </button>
      <h4 class=\"modal-title font-blue-ebonyclay\"> <i class=\"fa {$icon}\"></i>&nbsp;&nbsp;{$title} </h4>
    </div>
    ";
  }

  private function _pPopups()
  {
    $buffer = "";
    foreach( $this->_pCollection as $popupContent ){
      $buffer .= $popupContent;
    }
    return $buffer;
  }

  public function _pRender()
  { return "<div class=\"document-popup-container\">{$this->_pPopups()}</div>"; }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                           DATATABLE                                                 */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  public function datatable( $id, $header = array(), $body = array() )
  {
    return "
    <table class=\"table table-striped table-bordered table-hover dt-responsive\" id=\"{$id}\" data-datatable-tools=\"\" width=\"100%\" style=\"margin-bottom:0px;\">
      <thead>{$this->_dTableHeader($header)}</thead>
      <tbody>{$this->_dTableBody($body,$header)}</tbody>
  	</table>";
  }

  private function _dTableHeader( array $header = array() )
  {
    $buffer = '';
		foreach($header as $column){
      $style = ((isset($column['style']))?$column['style']:"");
      $order = ((isset($column['order']))?$column['order']:'');
			$buffer .= "<th style=\"{$style}\" data-datatable-order=\"{$order}\"> {$column['label']} </th>";
		}
    return $buffer;
  }

  private function _dTableBody( array $body = array(), array $header = array() ){
    $buffer = '';
    foreach( $body as $row ){
      $col = 0;
      $buffer .= "<tr>";
      foreach ( $row as $cell ){
        $style = ((isset($header[$col]['style']))?$header[$col]['style']:'');
	      $buffer .= "<td style=\"{$style}\">{$cell}</td>";
		    $col++;
		  }
      $buffer .= "</tr>";
    }
    return $buffer;
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       PORTLET                                                       */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////

  public function portlet( $type, $color, $icon = 'fa fa-gift', $subject = 'portlet', $helper = '', $content ='Content', $tools = array(), $actions = array(), $collapse = false )
  {
    $style = (($collapse)?'padding:0px 0px 0px 0px;':'');
    return "
    <div class=\"portlet {$type} {$color}\">
      {$this->_pTitle($icon, $subject, $helper, $tools, $actions)}
      <div class=\"portlet-body\" style=\"{$style}\">{$content}</div>
    </div>";
  }

  private function _pTitle( $icon, $subject, $helper = "", $tools, $actions )
  {
    $tools = $this->_pTools( $tools );
    $actions = $this->_pActions( $actions );
    return "
    <div class=\"portlet-title\">
      <div class=\"caption\">
        <i class=\"{$icon}\"></i>
        <span class=\"caption-subject\"> {$subject}</span>
        <span class=\"caption-helper\"> {$helper}</span>
      </div>
      <div class=\"tools\">{$tools}</div>
      <div class=\"actions\">{$actions}</div>
    </div>";
  }

  private function _pTools( array $tools = array() )
	{
		$b = '';
		foreach( $tools as $tool => $data ){
			switch( $tool ){
				case 'collapse':    $b .= '<a href="javascript:;" class="collapse"> </a>&nbsp;&nbsp;';      break;
				case 'fullscreen':  $b .= '<a href="javascript:;" class="fullscreen"> </a>&nbsp;&nbsp;';    break;
				case 'config':
          $b .= '<a href="javascript:;"
            data-simplicity-callback-type="click"
            data-simplicity-callback-action="'.$data['data-onclick'].'"
            class="config simplicity-callback"> </a>&nbsp;&nbsp;';

          break;
				case 'removed':     $b .= '<a href="javascript:;" class="remove"> </a>&nbsp;&nbsp;';        break;
        case 'button':
					if( !isset( $action['text'] )){          $action['text']         = 'Button Text';      }
					if( !isset( $action['button-class'] )){  $action['button-class'] = 'btn btn-default';  }
					if( !isset( $action['icon'] )){          $action['icon']         = 'fa fa-pencil';     }
					$b .= "<a class=\"{$action['button-class']} btn-sm\" href=\"javascript:;\"><i class=\"{$action['icon']}\"></i> {$action['text']} </a>";
				break;
				case 'ajax':
          $b .= "<a
            href=\"javascript:;\"
            class=\"reload\"
            data-url=\"/Request/Portlet{$data['data-url']}\"
            data-error-display=\"notific8\"
            data-load=\"true\">
          </a>";
				break;
			}
		}
		return $b;
	}

  private function _pActions( array $actions = array() )
	{
    $b = '';
		foreach( $actions as $action ){
			switch( $action['name']){
				case 'button':
					if( !isset( $action['text'] )){          $action['text']         = 'Button Text';        }
					if( !isset( $action['button-class'] )){  $action['button-class'] = 'btn btn-default';    }
					if( !isset( $action['icon'] )){          $action['icon']         = 'fa fa-pencil';       }
          if( !isset( $action['button-id'] )){     $action['button-id']    = 'unset';              }
					$b .= "
            <a id=\"{$action['button-id']}\" class=\"{$action['button-class']} btn-sm\" href=\"javascript:;\">
              <i class=\"{$action['icon']}\"></i>
              {$action['text']}
            </a>";
				break;
			}
		}
		return $b;
	}


  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       COLORS                                                        */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_colors = [
    "Crusta"  => "yellow-crusta",
    "Hoki"    => "blue-hoki",
    "Meadow"  => "green-meadow",
    "Sunglo"  => "red-sunglo",
    "Cascade" => "grey-cascade"
  ];


  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                       FORMATTING                                                    */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private function _fRow( $content )
  { return "<div class=\"row\">{$content}</div>"; }

  private function _fRowSingle( $content )
  { return "<div class=\"col-md-12 col-sm-12\">{$content}</div>"; }

  private function _fRowDouble( $content )
  { return "<div class=\"col-md-6 col-sm-12\">{$content}</div>"; }

  public function rowSingle( $content )
  { return $this->_fRow( $this->_fRowSingle( $content ) ); }

  public function rowDouble( $lContent, $rContent )
  {
    return $this->_fRow(
      $this->_fRowDouble( $lContent ) .
      $this->_fRowDouble( $rContent )
    );
  }

  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  /**                                           TAB SYSTEM                                                */
  //////////////////////////////////////////////////////////////////////////////////////////////////////////
  private $_tTab = [];
  private $_tTabSection = [];
  private $_tActive = 0;
  private $_tCount = 0;

  public function addTab( $text, $content, $badge = false, $count = false, $active = false, $name = 'tab' )
  {
    if( $active ){ $this->_tActive = $this->_tCount; }
    $this->_tTab[$this->_tCount] = ['index'=>$this->_tCount,'text'=>$text,'badge'=>$badge,'count'=>$count,'active'=>$active,'name'=>$name];
    $this->_tTabSection[$this->_tCount] = ['index'=>$this->_tCount,'content'=>$content,'active'=>$active,'name'=> $name];
    $this->_tCount++;
  }

  private function _tTab( $index, $text, $active, $badge = false, $count = false, $name = 'tab')
  {
    $li = "<li".(($active)?" class=\"active\">":">");
    $id = "{$name}_{$index}";
    $badge = (($count === false)?"":"<span class=\"badge badge-{$badge}\"> {$count} </span>");
    return "{$li}<a href=\"#{$id}\" data-toggle=\"{$name}\"> {$text} {$badge} </a></li>";
  }

  protected function _tSection( $index, $content, $active,  $name = 'tab' )
  {
    $id = "{$name}_{$index}";
    $class = "class=\"tab-pane".(($active)?' active':'')."\"";
    return "<div id=\"{$id}\" {$class}>{$content}</div>";
  }

  public function renderTabSystem()
  {
    return "<div class=\"tabbable-line\">
      <ul class=\"nav nav-tabs nav-tabs-lg\">{$this->_renderTabs()}</ul>
      <div class=\"tab-content\">{$this->_renderSections()}</div>
    </div>";
  }

  private function _renderTabs()
  {
    $b = "";
    foreach( $this->_tTab as $t ){
      $active = (($t['index'] === $this->_tActive)?true:false);
      $b .= $this->_tTab($t['index'],$t['text'],$active,$t['badge'],$t['count'],$t['name']);
    }
    return $b;
  }

  private function _renderSections()
  {
    $b = "";
    foreach($this->_tTabSection as $s){
      $active = (($s['index'] === $this->_tActive)?true:false);
      $b .= $this->_tSection($s['index'],$s['content'],$active,$s['name']);
    }
    return $b;
  }
}
