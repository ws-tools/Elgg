<?php
/**
 * Action called by AJAX periodic auto saving when editing.
 *
 * @package Blog
 */

$guid = get_input('guid');
$user = elgg_get_logged_in_user_entity();
$title = elgg_get_title_input();
$description = get_input('description');
$excerpt = get_input('excerpt');

// because get_input() doesn't use the default if the input is ''
if (empty($excerpt)) {
	$excerpt = $description;
}

// store errors to pass along
$error = false;

if ($title && $description) {
	if ($guid) {
		$entity = get_entity($guid);
		if ($entity instanceof ElggBlog && $entity->canEdit()) {
			$blog = $entity;
		} else {
			$error = elgg_echo('blog:error:post_not_found');
		}
	} else {
		$blog = new ElggBlog();
		$blog->subtype = 'blog';

		// force draft and private for autosaves.
		$blog->status = 'unsaved_draft';
		$blog->access_id = ACCESS_PRIVATE;
		$blog->title = $title;
		$blog->description = $description;
		$blog->excerpt = elgg_get_excerpt($excerpt);

		// mark this as a brand new post so we can work out the
		// river / revision logic in the real save action.
		$blog->new_post = true;

		if (!$blog->save()) {
			$error = elgg_echo('blog:error:cannot_save');
		}
	}

	// creat draft annotation
	if (!$error) {
		// annotations don't have a "time_updated" so
		// we have to delete everything or the times are wrong.

		// don't save if nothing changed
		$auto_save_annotations = $blog->getAnnotations([
			'annotation_name' => 'blog_auto_save',
			'limit' => 1,
		]);
		if ($auto_save_annotations) {
			$auto_save = $auto_save_annotations[0];
		} else {
			$auto_save = false;
		}

		if (!$auto_save) {
			$annotation_id = $blog->annotate('blog_auto_save', $description);
		} elseif ($auto_save instanceof ElggAnnotation && $auto_save->value != $description) {
			$blog->deleteAnnotations('blog_auto_save');
			$annotation_id = $blog->annotate('blog_auto_save', $description);
		} elseif ($auto_save instanceof ElggAnnotation && $auto_save->value == $description) {
			// this isn't an error because we have an up to date annotation.
			$annotation_id = $auto_save->id;
		}

		if (!$annotation_id) {
			$error = elgg_echo('blog:error:cannot_auto_save');
		}
	}
} else {
	$error = elgg_echo('blog:error:missing:description');
}

if ($error) {
	$json = ['success' => false, 'message' => $error];
	echo json_encode($json);
} else {
	$msg = elgg_echo('blog:message:saved');
	$json = ['success' => true, 'message' => $msg, 'guid' => $blog->getGUID()];
	echo json_encode($json);
}
exit;
