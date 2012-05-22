<?php

require_once('init.php');

?>

<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>University of Reddit</title>
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<?=PREFIX ?>/css/style.css">

  <script src="<?=PREFIX ?>/js/libs/modernizr-2.5.2.min.js"></script>
  <style type="text/css">
  p {
    margin: 0 0 5px 50px;
  }

  h1 {
    margin-top: 10px;
    margin-bottom: 10px;
  }

  h2 {
    margin-left: 25px;
    margin-bottom: 3px;
  }

  h3 {
    margin-left: 30px;
    margin-bottom: 3px;
  }

  ul {
    margin: 10px 0 10px 100px;
  }
-->
</style>
</head>
<body>
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  <?php
  require_once('header.php');
  require_once('social.php');

  if(isset($_GET['category_id']) && count($dbpdo->query("SELECT `id` FROM `objects` WHERE `id` = ? AND `type` = 'category' LIMIT 1", array($_GET['category_id'])) != 0))
    $active_category_id = $_GET['category_id'];
  else
    $active_category_id = -1;

  $catalog = new catalog($dbpdo);
  ?>
  <div id="main" role="main">
    <div id="help">
      <div class="content">
<?php

$str = <<<EOD
#Privacy Policy

##What information do we collect?

We collect information from you when you register on our site. 

When ordering or registering on our site, as appropriate, you may be asked to enter your name and, optionally, you e-mail address. You may, however, visit our site anonymously.

Google, as a third party vendor, uses cookies to serve ads. Google's use of the DART cookie enables it to serve ads to users based on their visit to our site and other sites on the Internet. Users may opt out of the use of the DART cookie by visiting the Google ad and content network privacy policy.

##What do we use your information for?

Any of the information we collect from you may be used in one of the following ways: 

* To personalize your experience (your information helps us to better respond to your individual needs)
* To improve our website (we continually strive to improve our website offerings based on the information and feedback we receive from you)
* To improve customer service (your information helps us to more effectively respond to your customer service requests and support needs)
* To send occassional emails

The email address you provide may be used to send you information, respond to inquiries, and/or other requests or questions.

##How do we protect your information?

We implement a variety of security measures to maintain the safety of your personal information when you enter, submit, or access your personal information. 

##Do we use cookies?

Yes. (Cookies are small files that a site or its service provider transfers to your computers hard drive through your Web browser (if you allow) that enables the sites or service providers systems to recognize your browser and capture and remember certain information.)

We use cookies to understand and save your preferences for future visits, keep track of advertisements and compile aggregate data about site traffic and site interaction so that we can offer better site experiences and tools in the future.

##Do we disclose any information to outside parties?

We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you, so long as those parties agree to keep this information confidential. We may also release your information when we believe release is appropriate to comply with the law, enforce our site policies, or protect ours or others rights, property, or safety. However, non-personally identifiable visitor information may be provided to other parties for marketing, advertising, or other uses.

##Third party links

Occasionally, at our discretion, we may include or offer third party products or services on our website. These third party sites have separate and independent privacy policies. We therefore have no responsibility or liability for the content and activities of these linked sites. Nonetheless, we seek to protect the integrity of our site and welcome any feedback about these sites.

##California Online Privacy Protection Act Compliance

Because we value your privacy we have taken the necessary precautions to be in compliance with the California Online Privacy Protection Act. We therefore will not distribute your personal information to outside parties without your consent.

As part of the California Online Privacy Protection Act, all users of our site may make any changes to their information at anytime by logging into the University of Reddit website, navigating to the top right hand menu, and selecting "settings". In most cases you will not have a specific profile outside of an account name and password; however, if you are going to teach you can elect to provide some background about yourself and why you are fit to teach on a certain subject. This information can be edited at any time by accessing your class page.

##Childrens Online Privacy Protection Act Compliance

We are in compliance with the requirements of COPPA (Childrens Online Privacy Protection Act), we do not collect any information from anyone under 13 years of age. Our website, products and services are all directed to people who are at least 13 years old or older.

##Your Content is Your Content

By using the University of Reddit we do not make any claims to ownership of the content you create or provide. We can however feature your content on a dedicated University of Reddit channel or use various aspects of your class (attributed) to help bring attention to it. The channels that we may use include, but are not limited to a Twitter feed, YouTube account, Vimeo account, communication on reddit, and newsletters. 

##Not Responsible For User Generated Content

The University of Reddit is not responsible for the content that its users post, share, or create for it. We will attempt to ensure that all information on the site are legal, and are not harmful to anyone's right's.

##Privacy Policy Coordinator

If you have any concerns or questions about any aspect of this policy, please feel free to contact our Privacy Policy Coordinator as follows: Privacy Policy Coordinator <universityofreddit@gmail.com>

##Terms and Conditions

Please also visit our Terms and Conditions section establishing the use, disclaimers, and limitations of liability governing the use of our website [here](http://ureddit.com/tos).

##Your Consent

By using our site, you consent to our privacy policy.

##Changes to our Privacy Policy

If we decide to change our privacy policy, we will post those changes on this page, and/or update the Privacy Policy modification date below. 

This policy was last modified on May 22, 2012.

##Contacting Us

If there are any questions regarding this privacy policy you may contact us at universityofreddit@gmail.com.

All logos and names are owned by their respective copyright holders.

EOD;

echo process($str);
?>
      </div>
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
