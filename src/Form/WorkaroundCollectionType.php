<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * This a workaround for the issue #37024.
 */
class WorkaroundCollectionType extends CollectionType
{
    /**
     * Use the original implementation for finishView() instead of the one, the one that cause the bug.
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($view->vars['prototype']->vars['multipart']) {
            $view->vars['multipart'] = true;
        }
    }
}
