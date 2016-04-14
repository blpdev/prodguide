<?php $CURRENT_PAGE = "quiz"; require_once("inc/header.php"); ?>



<div id="slickQuiz1" class="slickQuiz">
   <h1 class="quizName"><!-- where the quiz name goes --></h1>
   <div class="timer">
      <span class="minute">00</span>:<span class="second">00</span>
   </div>
   <div class="quizArea">
      <div class="quizHeader">
         <!-- where the quiz main copy goes -->
         <a class="button startQuiz" href="#">Get Started!</a>
      </div>
      <!-- where the quiz gets built -->
   </div>
   <div class="quizResults">
      <h3 class="quizScore">You Scored: <span><!-- where the quiz score goes --></span></h3>
      <h3 class="quizLevel"><strong>Ranking:</strong> <span><!-- where the quiz ranking level goes --></span></h3>
      <div class="quizResultsCopy">
          <!-- where the quiz result copy goes -->
      </div>
   </div>
</div>




<script>
$(document).ready(function() {
  var slick_quiz_1 = $('#slickQuiz1').slickQuiz({
     preventUnanswered : true,
     preventUnansweredText: "You must answer this question",
     randomSortQuestions: true,
     timerLength: 6000,
     warningTimer: 55,
	  //debug: true
     });
});
</script>

<?php require_once("inc/footer.php"); ?>