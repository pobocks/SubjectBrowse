<p>Settings for the SubjectBrowse plugin</p>
<div class="field">
  <label for="headings">Print headings for each letter:</label>
   <?php if (get_option('subject_browse_headers')){
           echo checkbox(array('name'=> 'headers', 'id' => 'headers', 'value' => 1), true) . '</div>';
         }
         else {
             echo checkbox(array('name'=> 'headers', 'id' => 'headers', 'value' => 1)) . '</div>';
         } ?>
<div class="field">
  <label for="alphabetical_skiplinks">Print skip links at top and bottom of page:</label>
   <?php if (get_option('subject_browse_alphabetical_skiplinks')){
           echo checkbox(array('name'=> 'alphabetical_skiplinks', 'id' => 'alphabetical_skiplinks', 'value' => 1), true) .'</div>';
         }
         else {
           echo checkbox(array('name'=> 'alphabetical_skiplinks', 'id' => 'alphabetical_skiplinks', 'value' => 1)) .'</div>';
         } ?>
         <p class="description">Note that if headers are turned off, skiplinks do not work.</p>
