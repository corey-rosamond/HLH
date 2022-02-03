<?php
/**
 * Datatable
 *
 * @package App\Objects
 * @version 1.0.0
 */
namespace Framework\Support\Object;

/**
 * Datatable
 *
 * As a building block for our new system we needed a faster more efficent way to create these objects. This is it
 */
class Datatable{

	/**
	 * We store the html for the datatable here while building it
	 * @var    string
	 * @access private
	 */
	private $html = "";

	/**
	 * If an id has been set it will be stored here
	 * @var    string
	 * @access private
	 */
	private $id;

	/**
	 * An arry of the all the column definitions
	 * @var array
	 * @access private
	 */
	private $columns;

  /**
   * Construct the datatable object
	 * @method __construct
	 * @return this      Return this for method chaining
	 * @access public
	 */
	public function __construct( $id, $columns = false )
  {
		$this->id = $id;
		if($columns){ $this->columns = $columns; }
		return $this;
	}

  /**
   * This is called when you are done adding and setting things on the datatable and want the html
   * @method finalize
   * @return string   All the html needed to build the datatable
   * @access public
   */
	public function finalize()
  {
		$return = '<table class="table table-striped table-bordered table-hover dt-responsive DataTbl" id="'.$this->id.'" data-datatable-tools="" style="margin:0px;" width="100%">';
      /** Start table header */
			$return .= '<thead><tr>';
				if(count($this->columns)>=1){
					foreach($this->columns as $column){
						$return .= '<th
							style="'.((isset($column['style']))?$column['style']:"").'"
							data-datatable-order="'.((isset($column['order']))?$column['order']:'').'">'.$column['label'].'</th>';
					}
				}
			$return .= '</tr></thead>';
      /** End table header */
			$return .= "<tbody>{$this->html}</tbody>";
		$return .= '</table>';
		return $return;
	}

	/**
	 * Add a row to the datatables
	 * @method addRow
	 * @param  array $data An array of the values to put in the row
	 * @return this        Return this for method chaining
	 * @access public
	 */
	public function addRow( $data ){
		$columnIndex = 0;
		$content = "";
		foreach ( $data as $cell ){
			$content .= "<td style=\"{$this->columnStyle( $columnIndex )}\">{$cell}</td>";
			$columnIndex++;
		}
		$this->html .= "<tr>{$content}</tr>";
    return $this;
	}

	private function columnStyle( $index )
	{
		if( !isset( $this->columns[$index]['style']) ){
			return "";
		}
		return $this->columns[$index]['style'];
	}


	/**
	 * Set the title attribute for the datatable itself
	 * @method setTitle
	 * @param  string   $title The title for the datatable
	 * @return this            Return this for method chaining
	 * @access public
	 */
	public function setTitle( $title ){
		$this->title = $title;
		return $this;
	}

	/**
	 * Set the Column titles and definitions
	 * @method setColumns
	 * @param  array     $columns  Set the column titles as well as their attributes
	 * @return this                Return this for method chaining
	 * @access public
	 */
	public function setColumns( $columns ){
		$this->columns = $columns;
		return $this;
	}


}
