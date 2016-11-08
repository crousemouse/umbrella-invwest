<?php

?>
<div class="row">
<div class="span12">

<?php echo $heroImage; ?>

</div><!-- End of span8 main content -->
</div>

<div class="home-grid clearfix">
<?php echo $homepagegrid; ?>
</div>

<div class="row-fluid awardwinningseries">
    <h1 class="sectiontitle">Award-Winning Series</h1>
    <h5 class="sectionsubtitle">Discover dogged reporting and powerful testimony.</h5>

    <div class="row-fluid series-cells">
    <?php echo $awardwinningseries; ?>
    </div>
</div>
<div class="row-fluid">
    <?php if (!dynamic_sidebar('Homepage Impact Image')) { ?>
        <p>Add an Image Widget widget to the Homepage Impact Image widget area, and set the caption to the photo credit. Leave other fields blank.</p>
    <?php }; ?>
</div>

<div class="row-fluid journalismwithimpact">
    <h1 class="sectiontitle">Journalism with Impact</h1>
    <h5 class="sectionsubtitle">Discover how InvestigateWest equips the public to make change.</h5>

    <div class="row-fluid series-cells">
    <?php echo $journalismwithimpact; ?>
    </div>
</div>