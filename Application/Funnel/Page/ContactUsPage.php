<?php
/**
 * ContactUsPage
 *
 * @package App\Page
 * @version 1.2.0
 */
namespace App\Page;

/**
 * ContactUsPage
 *
 * The ContactUsPage page
 */
class ContactUsPage extends \App\Abstracts\FunnelPage
{
  /** @var int Include the pageType */
  protected $_pageType = self::contactUs;
  /** @var boolean $_emailSent */
  protected $_emailSent;
  /** @var integer $_emailLogKey */
  protected $_emailLogKey;

  /**
   * Builds the content area and calls all sub functions to build
   * the ui specific to this page
   * @method _content
   * @param $data
   * @return string
   */
  protected function _content($data)
  {
    /** @var email sent status _emailSent */
    $this->_emailSent = $this->_cFunnel->customerSupportSent();
    /** Check if the email was sent */
    if($this->_emailSent){
      /** @var integer _emailLogKey add the email to the mailer log and return the key */
      $this->_emailLogKey = $this->_cFunnel->customerSupportLogKey();
      /** @var string _emailLogKey Format the string for human readability */
      $this->_emailLogKey = sprintf('#%08d',$this->_emailLogKey);
    }
    /** Now just let it run */
    if($this->_emailSent){
      /** they have sent a request already show them the thank you */
      return "
        <div id=\"funnel-content\" style=\"text-align:center;\">
          <h1>Thank you for contacting us!</h1>
          <p>Your contact reference number is {$this->_emailLogKey} please keep this for your records!</p>\n
        </div>\n
      ";
    }
    /** Write the form into the buffer */
    return "<div id=\"funnel-content\" style=\"text-align:center;\">
      <h3>{$data['support-phone-text']}{support-phone}</h3>
      <p>{$data['content']}</p>
      <form name=\"funnel-contact-us-form\" method=\"post\" action=\"\" id=\"funnel-contact-us-form\">
        <label for=\"name\" class=\"contactus-label\">
          <span class=\"contactus-step\">STEP 1:</span> Tell us your Full Name <span class=\"contactus-asterisk\">*</span>
        </label>
        <br /><br />
        <span class=\"contactus-position\">
          <span id=\"contactus-name-error\" class=\"contactus-error\"></span>
          <input name=\"name\" maxlength=\"80\" size=\"55\" class=\"contactus-input\" type=\"text\" id=\"name\">
        </span>
        <br /><br />
        <label for=\"email\" class=\"contactus-label\">
          <span class=\"contactus-step\">STEP 2:</span> Enter your Email Address <span class=\"contactus-asterisk\">*</span>
        </label>
        <br /><br />
        <span class=\"contactus-position\">
          <span id=\"contactus-email-error\" class=\"contactus-error\"></span>
          <input name=\"email\" maxlength=\"80\" size=\"55\" class=\"contactus-input\" type=\"email\" id=\"email\">
        </span>
        <br /><br />
        <label for=\"Subject\" class=\"contactus-label\">
          <span class=\"contactus-step\">STEP 3:</span> Pick the Nature of your issue <span class=\"contactus-asterisk\">*</span>
        </label>
        <br /><br />
        <select name=\"subject\" id=\"subject\" class=\"contactus-select\">
          <option value=\"General Informations\">General Informations - I have a question about your product</option>
          <option value=\"Technical Issue\">Technical Issue - I have encountered a technical problem</option>
          <option value=\"Sales Support\">Sales Support - I want more details about your offer</option>
          <option value=\"Refund Question\">Refund Question - I have a question regarding a refund</option>
        </select>
        <br /><br />
        <label for=\"message\" class=\"contactus-label\">
          <span class=\"contactus-step\">STEP 4:</span> Write us a Message <span class=\"contactus-asterisk\">*</span>
        </label>
        <br /><br />
        <span class=\"contactus-position\">
          <span id=\"contactus-message-error\" style=\"top:-95px;\" class=\"contactus-error\"></span>
          <textarea name=\"message\" maxlength=\"1000\" cols=\"57\" rows=\"8\" class=\"contactus-textarea\" id=\"message\"></textarea>
        </span>
        <br /><br />
        <label for=\"submit\" class=\"contactus-label\">
          <span class=\"contactus-step\">STEP 5:</span> Click the \"Submit Ticket\" <span class=\"contactus-asterisk\">*</span>
        </label>
        <br /><br />
        <input value=\"Submit Ticket\" id=\"funnel-contact-us-submit\" class=\"button\" type=\"submit\">
      </form>
    </div>";
  }
}
