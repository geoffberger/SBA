<span class="entry-details">Posted by 
<span class="author vcard"><?php print l($node->name, 'user' . $node->revision_uid, 
      array('attributes' => array('class' => 'fn nickname url', 'title' => 'View ' . $node->name . '\'s Profile'))); ?></span>
  on <abbr title="<?php print date('c', $node->created); ?>" class="published"><?php print date('n.j.y', $node->created); ?></abbr>
</span>

<?php print $node->body; ?>
