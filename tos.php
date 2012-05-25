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
  <?php include('fonts.php'); ?>

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
#Website Terms and Conditions of Use

##1. Terms

By accessing this website, you are agreeing to be bound by these website Terms and Conditions of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.

##2. Use License

Permission is granted to temporarily view a copy of the materials (information) on the University of Reddit's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:

* modify or copy the materials, unless by the explicit permission of the teacher;
* use the materials for any commercial purpose, or for any public display (commercial or non-commercial), unless owned by you;
* transfer the materials to another person or "mirror" the materials on any other server, unless by the explicit permission of the teacher.

This license shall automatically terminate if you violate any of these restrictions and may be terminated by University of Reddit at any time..

##3. Disclaimer

The materials on University of Reddit's website are provided "as is". University of Reddit makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Further, University of Reddit does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its Internet website or otherwise relating to such materials or on any sites linked to this site.

##4. Limitations

In no event shall University of Reddit or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption,) arising out of the use or inability to use the materials on University of Reddit's Internet site, even if University of Reddit or a University of Reddit authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.

##5. Revisions and Errata

The materials appearing on University of Reddit's website could include technical, typographical, or photographic errors. University of Reddit does not warrant that any of the materials on its website are accurate, complete, or current; however, we will do our utmost work to ensure that they are. University of Reddit may make changes to the materials contained on its website at any time without notice. University of Reddit does not, however, make any commitment to update the materials. As we update our Terms of Service we will inform you through either a specified post in our subreddit on reddit, or an alert on our site.

##6. Links

University of Reddit has not reviewed all of the sites linked to its Internet website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by University of Reddit of the site. Use of any such linked website is at the user's own risk.

##7. Site Terms of Use Modifications

University of Reddit may revise these terms of use for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these Terms and Conditions of Use.

##8. Governing Law

Any claim relating to University of Reddit's website shall be governed by the laws of the State of Pennsylvania without regard to its conflict of law provisions.
General Terms and Conditions applicable to Use of a Website.

##9. Medical and Legal Information Disclaimer

The University of Reddit is not a platform for the exchange of medical or legal information. While you may freely discuss these topics, you should not look to the follow any information without first consulting the proper professional (whether that be doctor or lawyer). The University of Reddit will not be held liable for any improper or potentially damaging information that is provided.

##10. Copyright Complaints:

If you believe that your information has been copied and is accessible on our website, or that the website contains links or other references to another online location that contains material or activity that infringes your copyright rights, you may notify the University of Reddit by providing the following information (as required by the Online Copyright Infringement Liability Limitation Act of the Digital Millennium Copyright Act, 17 U.S.C. sec. 512) to our copyright agent set forth below:

(i) A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.  
(ii) Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at that site.  
(iii) Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled, and information reasonably sufficient to permit the service provider to locate the material.  
(iv) Information reasonably sufficient to permit the service provider to contact the complaining party, such as an address, telephone number, and, if available, an electronic mail address at which the complaining party may be contacted.  
(v) A statement that the complaining party has a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law.  
(vi) A statement that the information in the notification is accurate, and under penalty of perjury, that the complaining party is authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.

##11. Editing And Deletions:

The University of Reddit reserves the right, but undertakes no duty, to review, edit, move or delete any material provided for display or placed on the website, in its sole discretion, without notice.

##Privacy Policy

Your privacy is very important to us. Accordingly, we have developed this Policy in order for you to understand how we collect, use, communicate and disclose and make use of personal information. The following outlines our privacy policy.

Please view our Privacy Policy [here](http://ureddit.com/privacy).

We are committed to conducting our business in accordance with these principles in order to ensure that the confidentiality of personal information is protected and maintained.

EOD;

echo process($str);
?>
      </div>
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
