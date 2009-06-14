<ol class="hfeed">
  <?php foreach($view->result as $result): ?>
    <li class="hentry">
      <h3><?php print l($result->node_title, 'node/' . $result->nid); ?></h3>
      <span class="entry-details">Posted by 
      <span class="author vcard"><?php print l($result->users_name, 'user' . $result->users_uid, 
            array('attributes' => array('class' => 'fn nickname url', 'title' => 'View ' . $result->users_name . '\'s Profile'))); ?></span>
        on <abbr title="<?php print date('c', $result->node_created); ?>" class="published"><?php print date('n.j.y', $result->node_created); ?></abbr>
      </span>
      <div class="entry-content"><?php print $result->node_revisions_body; ?></div>
      <span class="comments">
        <?php print '<a href="/node/' . $result->nid . '#comments">' . comment_count($result->node_comment_statistics_comment_count) . '</a>'; ?>
      </span>
    </li>
  <?php endforeach; ?>
</ol>
