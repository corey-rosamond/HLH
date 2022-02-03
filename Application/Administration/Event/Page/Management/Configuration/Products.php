<?php
/**
 * Dashboard
 *
 * @package App\Event\Management\Product
 * @version 1.0.0
 */
namespace App\Event\Page\Management\Configuration;

\Framework\_IncludeCorrect( APP_ROOT . "Abstracts" . DSEP . "FunnelPage.php" );
class Products extends \App\Abstracts\Page
{

  /**
   * Does this page require that the user be logged in
   * @var     boolean     $requireLogin
   * @access  protected
   */
  public static $requiresLogin = true;

  /**
   * Permission group required to view this page or false if none
   * @var     mixed     $permissionGroup  FALSE if none
   * @access  protected
   */
  public static $permissionGroup = false;

  /**
   * Permission required in the group defined above required to view this page
   * @var     mixed     $permission   FALSE if none
   * @access  protected
   */
  public static $permission = false;

  /**
   * These are the modals this page will use. Nothing needs to be done
   * Beyond this to get them the framework will just do it for you
   * @var     array       $modal
   * @access  protected
   */
  protected $modal = [ 'Orders.Holylandhealth' => null ];

  /**
   * Templates are a snipet oh html that we break into parts then
   * we transpose the data on top of the html and give it back
   * @var     array       $modal
   * @access  protected
   */
  protected $template = [];

  /**
   * Controllers are more function intensive they are in charge of controlling
   * groups of methods that logicly belong together
   * @var     array      $controller
   * @access  protected
   */
  protected $controller = [];

  /**
   * Page Header
   * @var     string      $pageHeader
   * @access  protected
   */
  protected $pageHeader = "Products: ";

  /**
   * Page Sub Header
   * @var     string      $pageSubHeader
   * @access  protected
   */
  protected $pageSubHeader = "View, edit, or add";

  /**
   * The 'key' of a product, if one is being edit4ed...
   * @var     string      $productSKU
   * @access  private
   */
   private $productKey;

  /**
   * This will build the body of the docment out the rest of the sections of the
   * page will be handled by the Application\Page abstract or by the
   * Advent\Page Abstract
   * @method  body
   * @param   array   $paramaters   This is an optional array of paramtars that can be passed
   * @return  string                The html needed to render the body for this page
   * @access  public
   */
  public function Body( array $paramaters = array() ){
    $this->setDocumentTitle( 'HolylandHealth: Products' );

    //If the user clicked on a SKU (to edit the product)
    if ( isset($_GET['action']) && ($_GET['action'] == 'editProduct')){
      if (!isset($_GET['key'])) {
        /* Note to self: When purpousfully throwing this Exception, I am getting
           Failed to include File(C:\xampp\htdocs\framework\Application\Administration\Event\Page\Management\Configuration\Exception.php)
           no such file or directory!  There may be something wrong with the
           extension handler.  Let Corey know...
        */
        throw new Exception("Missing key for product to edit.");
      }
      //Store this in the object so it can be accessed by other parts of it
      $this->productKey = $_GET['key'];

      //If the request method is POST, then the user is updating a submission.
      //Otherwise, they are editing it...
      if ($_SERVER['REQUEST_METHOD'] == 'POST')
      {
        //Display a confirmation to the user that their edit was successful
        $this->confirmEdit();
      }
      else
      {
        //Display a FORM with the INPUTs populated with the product's current values...
        $this->editProductBox();
      }

    }
    else { /* If the user did not click on a SKU to edit a product, then
              display the viewProducts modal and the addNewProducts modal */
      $this->viewProductsBox();
      $this->addNewProductBox();
    }

  }

/**
 * Display a confirmation to the user that their changes were committed
 * @method  confirmEdit
 * @access  private
 */
  private function confirmEdit()
  {

    //Do we have a product key?  We cannot proceed without one.
    if (isset($_POST['key']))
    {
      $keyInt = intval($_POST['key']);
      if (!is_int($keyInt))
      {
        throw new Exception("Product key is not valid.");
      }
    }
    else
    {
      throw new Exception("Product key is missing.");
    }

    //No validation required on the SKU, as of right now.

    //Check for a product name.  We will need that.
    if (isset($_POST['name']) && gettype($_POST['name']) == 'string')
    {
      $prodName = $_POST['name'];
      if (!(strlen($prodName) > 0)) {
        throw new Exception("Product name cannot be blank.");
      }
    }
    else {
      throw new Exception("Product name missing or invalid.");
    }

    //Check for a product description.  Even if it is blank, but submitted, we will be fine.
    if (isset($_POST['description']) && gettype($_POST['description']) == 'string')
    {
      $prodName = $_POST['name'];
    }
    else {
      throw new Exception("Product description invalid.");
    }

    $price  = $_POST['price'];
    $cost   = $_POST['cost'];
    $weight = $_POST['weight']; //There is a problem with weight...

    //Note that if is-digital is not checked, nothing will be in the POST.
    //Therefore, if it is submitted, it is checked.
    $isDigital = (isset($_POST['is-digital']) ? TRUE : FALSE);
    $image = $_POST['prod-image-url'];

    $query = "UPDATE `funnel-products`
      SET `sku` = :sku,
          `name` = :name,
          `description` = :description,
          `price` = :price,
          `cost` = :cost,
          `weight` = :weight,
          `is-digital` = :is-digital,
          `image` = :image
      WHERE `key` = :key";
    $preparedStatement = $this->prepare($query);
    $preparedStatement->bindValue(':key', 		    $keyInt,  \PDO::PARAM_INT);
    $preparedStatement->bindValue(':name', 	     $type, 	 \PDO::PARAM_STR);
    $preparedStatement->bindValue(':description', $message, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':price',       $price,   \PDO::PARAM_STR);
    $preparedStatement->bindValue(':cost',        $cost,    \PDO::PARAM_STR);
    $preparedStatement->bindValue(':weight',      $weight,   \PDO::PARAM_STR);
    $preparedStatement->bindValue(':is-digital',  $isDigital, \PDO::PARAM_STR);
    $preparedStatement->bindValue(':image',       $image,   \PDO::PARAM_STR);

    $this->writeBody(print_r($preparedStatement, true));
    //$this->writeBody(print_r($_POST, true));
  }

  private function editProductBox()
  {
    //$this->writeBody("Edit product box to be placed here.");
    $productBoxConfig = [
      'title'           => 'Edit product',
      'icon'            => 'icon-globe',
      'fontColor'       => 'font-green uppercase',
      'backgroundColor' => "white",
      'type'            => 'light bordered',
      'portletStyles'   => "width:45%;",
      'bodyStyles'      => "padding:20px;"
    ];
    //public function __construct( $title, $content=false, $actions = false, $type = false, $icon=false, $fontcolor=false, $color = false ){
    $Portlet = new \Framework\Support\Object\Portlet( $productBoxConfig );

    //Getting the Products controller
    //This returns from \framework\Commander\Products.php
    $prodController = \Framework\Core\Receptionist::controller( 'Products', true );


    //THIS WRITES THE HTML OF THE EDIT PRODUCT INPUT
    $Portlet->buffer(
      //This displays the FORM input to edit the product...
      $prodController->editProduct($this->productKey) //No semicolon as this is a function argument...
    );

    //Portlet rendered correctly
    $this->writeBody($Portlet->render());

  }

  private function addNewProductBox() {
    /** @var array This is the portlet configuration for the Add New Product box */
    $addNewProductConfig = [
      'title'           => 'Add new product',
      'icon'            => 'icon-plus',
      'fontColor'       => 'font-green uppercase',
      'backgroundColor' => "white",
      'type'            => 'light bordered',
      'portletStyles'   => "width:45%;",
      'bodyStyles'      => "padding:20px;"
    ];
    /** @var \Framework\Support\Object\Portlet Make a portleet for the section */
    $Portlet = new \Framework\Support\Object\Portlet( $addNewProductConfig );
    /** @var array This is the array where we will have our products... */

    $addForm = <<<EOD
      <form action="" method="POST">
      <input name="product-name" placeholder="product name" type="text">
      <label for="name">Product Name</label><br>

      <input name="sku" placeholder="product SKU" type="text">
      <label for="sku">SKU</label><br>

      <input name="price" placeholder="0.00" type="number" max="1000" min="0.01" step="0.01" />
      <label for="price">Price <sub>$</sub></label><br>

      <input name="cost" placeholder="0.00" type="number" max="1000" min="0.01" step="0.01" />
      <label for="cost">Cost <sub>$</sub></label><br>

      <input name="weight" placeholder="0.00" type="number" max="1000" min="0.01" step="0.01" />
      <label for="weight">Weight <sub>Lbs</sub></label><br>

      <textarea name="description"></textarea>
      <label for="description">Description</label><br>

      <input name="is-digital" type="checkbox">
      <label for="is-digital">Is digital</label>

      <br>
      <input type="submit" value="Add Product" />
    </form>
EOD;
    $Portlet->buffer($addForm);
    //Portlet rendered correctly
    $this->writeBody( $Portlet->render() );
  }

  private function viewProductsBox() {
    $productBoxConfig = [
      'title'           => 'Products',
      'icon'            => 'icon-globe',
      'fontColor'       => 'font-green uppercase',
      'backgroundColor' => "white",
      'type'            => 'light bordered',
      'portletStyles'   => "width:45%;",
      'bodyStyles'      => "padding:20px;"
    ];
    //public function __construct( $title, $content=false, $actions = false, $type = false, $icon=false, $fontcolor=false, $color = false ){
    $Portlet = new \Framework\Support\Object\Portlet( $productBoxConfig );

    //Getting the Products controller
    //This returns from \framework\Commander\Products.php
    $prodController = \Framework\Core\Receptionist::controller( 'Products', true );

    //This gives us the product list
    $productListResult = $prodController->getProductList();
    //$Portlet->buffer("Display product stuff here... " . $stuff);
    $Portlet->buffer($productListResult);

    //Portlet rendered correctly
    $this->writeBody($Portlet->render());

  }
}
