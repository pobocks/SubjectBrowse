<?php
/**
 * The Reference index controller class.
 *
 * @package Reference
 */
class Reference_IndexController extends Omeka_Controller_AbstractActionController
{
    public function browseAction()
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        // Remove disabled slugs.
        foreach ($slugs as $slug => $slugData) {
            if (empty($slugData['active'])) {
                unset($slugs[$slug]);
            }
        }
        if (empty($slugs)) {
            throw new Omeka_Controller_Exception_404;
        }
        $this->view->references = $slugs;

        $types = array();
        foreach ($slugs as $slug => $slugData) {
            $types[$slugData['type']] = true;
        }
        $this->view->types = $types;
    }

    /**
     * Alphabet action.
     */
    public function listAction()
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        if (empty($slugs)) {
            $this->forward('browse', 'items', 'default');
        }

        $slug = $this->getParam('slug');
        if (!isset($slugs[$slug]) || empty($slugs[$slug]['active'])) {
            throw new Omeka_Controller_Exception_404;
        }

        $this->view->slug = $slug;
        $this->view->slugData = $slugs[$slug];
        $this->view->references = $this->view->reference()->getList($slug);
    }

    /**
     * Hierarchy action.
     */
    public function treeAction()
    {
        if (!get_option('reference_tree_enabled')) {
            $this->forward('browse', 'items', 'default');
        }
        $this->view->subjects = $this->view->reference()->getTree();
    }
}
