<?php

/**
 * Provides a block with the feedback dataset use form.
 *
 * @Block(
 *   id = "datatank_feedback_dataset_use_block",
 *   admin_label = @Translation("Feedback dataset use block"),
 * )
 */

namespace Drupal\datatank\Plugin\Block;

use Drupal\Core\Block\BlockBase;

class FeedbackDatasetUseBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
    public function build() {
        return \Drupal::formBuilder()->getForm(\Drupal\datatank\Form\FeedbackDatasetUseForm::class);
    }
}

?>