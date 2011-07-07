<div class="field">
  <label for="headings">Print headings:</label>
   <?php if (get_option('subject_browse_headers')){
           echo checkbox(array('name'=> 'headers', 'id' => 'headers', 'value' => 1), true);
         }
         else {
           echo checkbox(array('name'=> 'headers', 'id' => 'headers', 'value' => 1));
         }
         echo '<p class="explanation">Print headers for each section (#0-9 and symbols, A, B, etc.)</p></div>';
    ?>
<div class="field">
  <label for="alphabetical_skiplinks">Print skip links:</label>
   <?php if (get_option('subject_browse_alphabetical_skiplinks')){
           echo checkbox(array('name'=> 'alphabetical_skiplinks', 'id' => 'alphabetical_skiplinks', 'value' => 1), true);
         }
         else {
           echo checkbox(array('name'=> 'alphabetical_skiplinks', 'id' => 'alphabetical_skiplinks', 'value' => 1));
         }
         echo '<br /><br /><p class="explanation">Print skiplinks at the top and bottom of each page, which link to the alphabetical headers.</p><p class="explanation">Note that if headers are turned off, skiplinks do not work.</p></div>';
    ?>
<div class="field">
  <label for="item_links">Add Links in Item pages:</label>
   <?php if (get_option('subject_browse_item_links')){
           echo checkbox(array('name'=> 'item_links', 'id' => 'item_links', 'value' => 1), true);
         }
         else {
           echo checkbox(array('name'=> 'item_links', 'id' => 'item_links', 'value' => 1));
         }
         echo '<p class="explanation">Makes subject links in item display pages clickable links.</p></div>';
    ?>

