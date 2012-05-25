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
#Help and FAQs

## Basics

**What is University of Reddit?**  
The University of Reddit is the product of free intellectualism and is a haven for the sharing of knowledge. Teachers and students are free to explore any subject that interests them.

Unlike a conventional university, University of Reddit strives to make its course offerings free, varied, and easily accessible. Teachers are given complete freedom in planning their curricula and how they want to carry out their class. Students don't have to worry about attendance, grades, or tuition - this isn't a regular university.

**What is University of Reddit's mission?**  
University of Reddit aims to become a community for anyone looking to teach or learn. We strive to provide an environment in which students need not pay tuition or dispense personal information.

**Are you affiliated with reddit?**  
The short answer is "not yet". However, we are working on it. In the meantime, check out our [subreddit](http://www.reddit.com/r/UniversityofReddit/). It's where we started out, after all!

**Can anyone submit a class?**  
Yes. However, before creating a class, clear any potential obstructions that may prevent you from following through with it. Teachers are reminded that, although everyone is welcome to teach, you are making a commitment. You have an obligation to complete a course once you begin it.

Additionally, a class does not have to be taught by one teacher. Co-teachers are welcomed and even encouraged. Classes can even be taught by a group of students! An example of a class of this nature already exists within University of Reddit and, thus far, has been successful.

**Can we get creddited (lame, I know) for completing classes?**  
No. UoR is not an accredited university so we cannot distribute diplomas or grant credit for completed classes. There is an awards system in the works but, in the meantime, make sure to leave a nice karma tip for good teaching.

**Is University of Reddit available in other languages?**  
While we are trying to reduce our anglocentrism, our website is completely in English. If you are interested in translating to make the site more accessible, [drop us a line]( mailto:anastas@ureddit.com) or [PM the moderators](http://www.reddit.com/message/compose?to=%23UniversityofReddit) on Reddit.

**Who's in charge here?**  
While we strive to make University of Reddit as self-sustaining as possible, the moderators (eawesome3, amberamberamber, justrasputin and aveman101) are here to iron out any bugs on the website. Should you find anything wrong or have questions, don't hesitate to get in touch - we're more than happy to help.

## How do I get involved?

### Students

**Take classes!**  
Taking classes is a great way to fulfill University of Reddit's mission. We do ask that students put a bit of thought before committing to a class. In doing so, students that are actually keen on taking a certain class will have the opportunity to engage and interact with students of a similar mindset. Additionally, depending on the course's structure, failure to contribute may negatively affect the overall outcome of the class.

**How do I sign up to take courses?**  
Courses can be viewed in our [catalog](http://universityofreddit.com/). To sign up for courses, you will need to [register](http://universityofreddit.com/register) an account on our site to be an official member of that course's roster.

To  add a course to your schedule, click the "+add" button (located on the left side of the course title). Your schedule can be found by clicking on your username, located on the right side of the navigation bar. Some courses may require you to contact the teacher in order to be able to access the materials. Since there is not a set standard for how to conduct a course, instructors' methods of teaching may vary.

**Can I drop courses?**  
Absolutely! There are no limitations as to when you can leave, nor are there any repercussions.

**I want to take a class that isn't offered. What should I do?**  
Find a relevant subreddit and ask its userbase! In doing so, it's highly  probable that someone will offer to teach the class and they'll be an  expert on that topic. Alternatively, you can ask within our [subreddit](http://www.reddit.com/r/UniversityofReddit/).

### Teachers

**Sign up to teach a class!**  
Your class can be about anything academic or nonacademic. Once you have registered an account with us, you can create a class by clicking "become a teacher" in the top right (or by clicking [here](http://universityofreddit.com/teach)).

*Remember:*  By creating a class, you are a making a commitment to teach it. If you have  any doubts about capability of following through with the class, it's better to not teach it at all. Lesson plans require a considerable amount of forethought and teachers are also expected to answer questions outside of class! If you have any doubts about whether you will be able to stick it out, consider preparing half of your course's lectures/material before you announce it.

Once you have created a class, you should announce it by submitting it to [our reddit](http://reddit.com/r/UniversityofReddit) - make sure you start the title with "[Class]."


## Resources

**What tools does UReddit provide?**

After you've created a class, you will find a "teacher admin panel" link in the top right. From there, you can manage your class and you can send a mass PM to everyone that has added your class. If you don't already know, every user is automatically given an email address (their username @ureddit.com) and have the choice to set up a forwarding address; PMs get sent not only to users' PM inboxes but also as an email to their email inboxes. This is a good way to stay in touch with your students on UReddit itself. (Of course, if they reply to an email, their response will bypas your PM inbox and go directly to your @ureddit mailbox, so you should use UReddit's email service as well.)

We also provide a file hosting service. Check [this blog post](http://ureddit.com/blog/2012/02/23/ureddit-filehosting-for-teachers/) for details.

We also provide both Ventrillo and Murmur voice chat servers with several channels each. Each service may be accessed using the appropriate client by connecting to ureddit.com with otherwise standard settings (no password necessary).

**What third-party tools are available to me?**

We also suggest looking at the following options for making your class easier to manage:

* Use [Doodle](http://www.doodle.com/) to find a mutually convenient time to hold your class
* Host your class on [Google Groups](http://groups.google.com)
* [Tokbox](http://www.tokbox.com/) is great for presentation-based curricula and gives you the ability to present PowerPoint or PDF slides that you have uploaded to [Slideshare](http://slideshare.net)
* [Scriblink](http://www.scriblink.com/) is a digital collaborative whiteboard with VoIP functionality
* [Vokle](http://vokle.com/) allows you to field questions from your class in real-time

We are also currently developing our own open-source classroom software - keep an eye on our [blog](http://universityofreddit.com/blog) for news.

**Where can I interact with the University of Reddit community?**  
Make sure you visit #universityofreddit on the freenode IRC network. If you are a beginner with IRC, here is a [basic tutorial](http://irchelp.org/irchelp/irctutorial.html) to get you started. We also have a class on IRC for those interested. 

## Website FAQ

**I've lost my password! What's the next logical step?**  
First, think very hard. Where did you last put it? If you still can't remember, you can go to the [password recovery page](http://universityofreddit.com/recover_password).

*Note:* You must have registered with an email address, otherwise we won't know to whom to send the new account password. If you didn't input your email address, you are out of luck - make another account.

**Something's wrong with the site! Who should I tell?**  
Tell [us](mailto:anastas@ureddit.com). Make sure to go into as much detail as possible and thanks in advance!

**Will you do anything malicious with my information?**  
No, why would we do that? We want users to register accounts so that we can eliminate spam on the website. Additionally, because we're not officially affiliated with reddit, you are more than welcome (in fact, encouraged) to use a different password than that of your proprietary reddit account.

We ask for your email so that, if anything goes wrong with your account, we can reset your password. We're much too lazy to send out daily newsletters. Also, we have nothing to sell.

**Miscellaneous**  
If you have suggestions or resources that might contribute to making   University of Reddit better, email [us](mailto:anastas@ureddit.com) or  [PM the mods](http://www.reddit.com/message/compose?to=%23UniversityofReddit)!

EOD;

echo process($str);
?>
      </div>
    </div>
  </div>
  <?php require_once('footer.php'); ?>
</body>
</html>
