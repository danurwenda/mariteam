<h2>Enter your keywords</h2>
<input onkeypress="return runScript(event)" type="text" placeholder="Search ..." class="nav-search-input" autocomplete="off" />

<h2>Search Results</h2>

<?php foreach ($results as $r) {
    ?>
    <a href="<?php echo $r->link; ?>"><h3><?php echo $r->title; ?></h3></a>
    <p><?php
    echo $r->text;?></p><?php
    }?>