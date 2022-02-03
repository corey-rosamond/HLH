<?php

namespace Framework\Commander;

class Products extends \Framework\Support\Abstracts\Singleton
{

  private $_cDocument;

  /** Modals */
  private $_mProducts; //Plural!

  /** Tab Datatable Variables */
  private $_dTabPortletType  = 'box';
  private $_dTabPortletColor = 'blue-chambray';
  private $_dTabPortletIcon  = "fa fa-shopping-cart";
  private $_dTabPortletTitle = "Products";
  private $_dTabDatatableID  = "ProductsDatatable";
  private $_dTabDatatableCol = [
    ["label"=>"Key",        "style"=>"width:5%",       "order"=>true],
    ["label"=>"SKU #",        "style"=>"width:10%",    "order"=>true],
    ["label"=>"Product",      "style"=>"width:15%",    "order"=>true],
    ["label"=>"Description",  "style"=>"width:50%",    "order"=>true],
    ["label"=>"Price",        "style"=>"width:5%",     "order"=>true],
    ["label"=>"Cost",         "style"=>"width:5%",     "order"=>true],
    ["label"=>"Weight",       "style"=>"width:8%",     "order"=>true],
    ["label"=>"Digital",      "style"=>"width:2%",     "order"=>true]
  ];

  //Truncation length for product descriptions (in characters)
  private $_dDescLen = 150;

  public static function construct()
  {
    $instance = self::getInstance();
    $instance->_cDocument = \Framework\Core\Receptionist::controller( 'Document', true );
    $instance->_mProducts = \Framework\Core\Receptionist::modal("Products", "Holylandhealth", true);
    return $instance;
  }

  /**
   * Get the products associated with an order
   * @method getOrderProducts
   * @param $orderKey
   * @return mixed
   */
  public function getOrderProducts($orderKey)
  { return $this->_mProducts->getOrderProducts($orderKey); }


  public function productsCount()
  {
    //return $this->_mProducts->countOrderMessages();
    return $this->_mProducts->count();  //I have not yet checked where this is defined.
  }

  public function addProduct( $sku, $product, $description, $price, $cost, $weight, $digital)
  {
    // return $this->_mOrderMessages->addOrderMessage( $orderKey, $userKey, $message );

    return $this->_mAddProduct->addProduct( $sku, $product, $description, $price, $cost, $weight, $digital );
    //Like the previous method, I have not, yet, checked where this is defined... OR if it is defined.
  }

  public function getProduct( $key )
  { return $this->_mProducts->getProduct( $key ); }


  public function editProduct($key) {
    //$sku was successfully passed into here.

    //Receptionist gies us the Framework Commander Products object
    $products = \Framework\Core\Receptionist::controller('Products', true);

    //Assign the Products modal FROM that Products object to this $products variable,
    //since it's those functions we want to use...
    $products = $products->_mProducts;
    //Confirmed method_exists($products, 'getProduct') returns TRUE at this point.

    //Product returned by getProduct.
    $gpProduct = $products->getProduct($key);

    if (!array_key_exists('key', $gpProduct)) {
      /* Every product returned by getProduct should have a 'key' key in its result.
         If it doesn't, then the product with that key does not exist in the products
         table.  In that case, throw an exception.
      */
      throw new Exception("Unable to get Product.");
    }
    else {
      /* If we made it this far, that means we got a product back.  Display the
         FORM input to edit this product... */

      //Do the HTML-friendly formatting, here, so that we can pass the variables into the Heredoc
      $key =         htmlspecialchars($gpProduct['key']);
      $sku =         htmlspecialchars($gpProduct['sku']);
      $name =        htmlspecialchars($gpProduct['name']);
      $description = htmlspecialchars($gpProduct['description']);


      $price =       $gpProduct['price'];
      //Format the price, even more, using sprintf, so that it is in a familar format to the end-user
      //First, check and make sure the price is not blank/null.
      if (strlen($price) > 0) {
        $price =     sprintf("%01.2f", $price);
      }
      $price =       htmlspecialchars($price);

      //Format the cost, even more, using sprintf, so that it is in a familar format to the end-user
      //First, check and make sure the cost is not blank/null.
      $cost =        $gpProduct['cost'];
      if (strlen($cost) > 0) {
        $cost =      sprintf("%01.2f", $cost);
      }
      $cost =        htmlspecialchars($cost);

      $weight =      $gpProduct['weight'];
      if (strlen($cost) > 0) {
        $weight =    sprintf("%01.2f", $weight);
      }
      $weight =      htmlspecialchars($weight);

      $price =       htmlspecialchars($price);

      $imgURL =      $gpProduct['image'];  //No specialchars here since this is a URL and not innerHTML.

      /* The funnel-products table has a field called is-digital
         That field is a tinyint(1).  1 is true and 0 is false.
         We will use this to include the presence of the 'checked' attribute
         of that checkbox input */
      $isDigital =   ($gpProduct['is-digital'] == 1) ? "checked" : "";

      $returnHTML = <<<EOT
<b>Product ID: {$gpProduct['key']}</b>
<form action="" id="edit-prod" method="post">

<input id="prod-key" type="hidden" name="key" value="$key">

<input id="prod-sku" type="text" name="sku" value="$sku">
<label for="prod-sku">SKU</label><br>

<input id="prod-name" type="text" name="name" value="$name">
<label for="prod-sku">Name</label><br>

<textarea id="prod-description" cols=18 rows=5 name="description">
$description
</textarea>
<label for="prod-sku">Description</label><br>

<input id="prod-price" type="text" name="price" value="$price">
<label for="prod-price">Price<sub>($)</sub></label><br>

<input id="prod-cost" type="text" name="cost" value="$cost">
<label for="prod-sku">Cost<sub>($)</sub></label><br>

<input id="prod-weight" type="text" name="weight" value="$weight">
<label for="prod-sku">Weight<sub>(Lbs.)</sub></label><br>
EOT;
      //Check to see if we have an image URL...
      if (strlen($imgURL) > 1) {
        //If so then display the image to the end user so they know what URL is currently set...
        $returnHTML .= '
<img alt="Product Image" src="' . $imgURL . '" style="max-width: 200px"><br>';
        /* Note, we check if the length is greater than 1 (rather than 0) to
           try and workaround some rogue newline or whitepace character being
           the only character in the URL string in this table field. */
      }


      $returnHTML .= <<<EOT
<input id="prod-image-url" style="min-width:450px" type="url" name="prod-image-url" value="$imgURL"><br>
<label for="prod-image-url">Product Image URL</label><br><br>

<input id="prod-is-digital" type="checkbox" name="is-digital" $isDigital value="true">
<label for="prod-is-digital">Is Digital?</label><br>

<input type="submit" value="Save">
</form>
EOT;
      return $returnHTML;
    }
  }



  public function productsDatatable( ) //Removed key from arguments
  {
    return $this->_cDocument->portlet(
      $this->_dTabPortletType,
      $this->_dTabPortletColor,
      $this->_dTabPortletIcon,
      $this->_dTabPortletTitle,'',
      $this->_cDocument->datatable(
        $this->_dTabDatatableID,
        $this->_dTabDatatableCol,
        /*
          $instance->_mProducts = \Framework\Core\Receptionist::modal( "Products", "Holylandhealth", true );
          That gives us \Framework\Modulus\Modal\Products.php, which has a getProdcuts() method
        */
        $this->_formatDatatable($this->_mProducts->getProducts( ))
      ),[],[],true
    );
  }

  private function _formatDatatable( $products )
  {
    //If there are fields we do not want, edit the modulus\modal\products.php file
    foreach( $products as &$product ){
      $product['key'] = "<a href=\"?action=editProduct&key=" . urlencode($product['key']) . "\">" . $product['key'] . "</a>";
      $product['sku'] = $product['sku'];
      $product['price']  = sprintf("$%01.2f", $product['price']);
      $product['cost']   = sprintf("$%01.2f", $product['cost']);
      $product['description'] = $this->truncateDescription(strip_tags($product['description']));
      //Note the weight UOM is displayed in the User Agent as 'LBS'.
      //That is because of front-end CSS.  The .uppercase class in components.css.
      $product['weight'] = sprintf("%01.1f Lbs", $product['weight']);
      $product['is-digital'] = ($product['is-digital'] ? "Yes" : "No");
    }
    return $products;
  }

  private function truncateDescription($descInput) {
    if (strlen($descInput) > $this->_dDescLen) {
      return substr($descInput, 0, $this->_dDescLen) . "...";
      //Place an elipses so the user knows the product desc has been truncated.
    }
    return $descInput;
  }

  /**
   * Returns a list of products
   * @return array
   */
  public function getProductList($limit = 100) {
    //$returnBody = "product list placeholder"; //Work this stuff back in
    return $this->productsDatatable();
  }
}
