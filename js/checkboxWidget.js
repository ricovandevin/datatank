/**
 * @file
 * Handles click on "fake" checkboxes on the widget checkboxWidget.php
 */

(function ($, Drupal, drupalSettings) {

    'use strict';

    /**
     * Handles the click event on the checboxes, launches the <a> tag.
     *
     * @type {Drupal~behavior}
     *
     * @prop {Drupal~behaviorAttach} attach
     */
    Drupal.behaviors.CheckBoxWidget = {};
    Drupal.behaviors.CheckBoxWidget.attach = function () {
        $('.facets-checkbox').click(function () {
            $(this).parent('.form-type-checkbox').find('a')[0].click();
        });
    };


})(jQuery, Drupal, drupalSettings);

