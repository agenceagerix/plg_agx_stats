<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.titlecharcount
 * @version     1.0.0
 * @author      Emmanuel Danan
 * @copyright   Copyright (C) 2025
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

/**
 * Plugin system to inject a character counter for article titles
 *
 * @since 1.0.0
 */
class PlgSystemCharCount extends CMSPlugin
{
    /**
     * Load the language file on instantiation
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Event triggered before the final HTML output is sent to the browser.
     *
     * @return void
     * @since 1.0.0
     * @throws Exception
     */
    public function onAfterDispatch(): void
    {
        $app = Factory::getApplication();

        // Only inject in the administrator site
        if (!$app->isClient('administrator')) {
            return;
        }

        $input = $app->input;

        // Only on com_content article edit view
        if (
            $input->getCmd('option') !== 'com_content' ||
            $input->getCmd('view') !== 'article' ||
            $input->getCmd('layout') !== 'edit'
        ) {
            return;
        }

        // Get parameters
        $plugin = JPluginHelper::getPlugin('system', 'charcount');
        $params = new JRegistry($plugin->params);

        // Check if the checkboxes for title and meta description are checked or not
        $isActivatedTitle = $params->get('show_title', '0');
        $isActivatedMeta = $params->get('show_meta', '0');

        // Get the maximum length of the title configured in the plugin's parameters
        $valMaxTitle = $params->get('long_max_title', '70');
        $valMaxMeta = $params->get('long_max_meta', '300');

        // If the counter for the title is activated...
        if ($isActivatedTitle == '1') {
            ?>

            <script type="text/javascript">
                // @since 1.0.0

                document.addEventListener('DOMContentLoaded', function () {
                    const titleInput = document.getElementById('jform_title');

                    titleInput.style.position = 'relative';
                    titleInput.style.paddingRight = '75px';

                    if (!titleInput) return;

                    // Create the counter element
                    const counter = document.createElement('span');
                    counter.id = 'title-char-counter';
                    counter.style.display = 'inline-block';
                    counter.style.position = 'absolute';
                    counter.style.right = '8px';
                    counter.style.zIndex = '10';
                    counter.style.transform = 'translateY(-50%)';
                    counter.style.top = '50%';

                    // Update function
                    const updateCounter = () => {
                        const count = titleInput.value.length;
                        // If the current number of characters is greater than the maximum...
                        if (count > <?php echo $valMaxTitle ?>) {
                            // Turn the text red
                            counter.style.color = 'red';
                        }
                        else {
                            // Else, turn the text green
                            counter.style.color = 'green';
                        }
                        counter.textContent = `${count} / <?php echo $valMaxTitle ?>`;
                    };

                    // Insert the counter after the input
                    titleInput.parentNode.appendChild(counter);

                    // Listen for input events
                    titleInput.addEventListener('input', updateCounter);

                    // Initialize
                    updateCounter();
                });
            </script>
        
            <?php
        }

        // If the counter for the meta description is activated...
        if ($isActivatedMeta == '1'){
            ?>

            <script type="text/javascript">
                // @since 1.0.0

                document.addEventListener('DOMContentLoaded', function () {
                    const metaInput = document.getElementById('jform_metadesc');

                    const prevCounter = document.getElementsByClassName('text-muted')[0];
                    prevCounter.style.visibility = 'hidden';

                    metaInput.style.position = 'relative';
                    metaInput.style.paddingRight = '75px';

                    if (!metaInput) return;

                    // Create the counter element
                    const counter = document.createElement('span');
                    counter.id = 'metadesc-char-counter';
                    counter.style.display = 'inline-block';
                    counter.style.position = 'absolute';
                    counter.style.right = '8px';
                    counter.style.zIndex = '10';
                    counter.style.transform = 'translateY(-50%)';
                    counter.style.top = '50%';

                    // Update function
                    const updateCounter = () => {
                        const count = metaInput.value.length;
                        // If the current number of characters is greater than the maximum...
                        if (count > <?php echo $valMaxMeta ?>) {
                            // Turn the text red
                            counter.style.color = 'red';
                        }
                        else {
                            // Else, turn the text green
                            counter.style.color = 'green';
                        }
                        counter.textContent = `${count} / <?php echo $valMaxMeta ?>`;
                    };

                    // Insert the counter after the input
                    metaInput.parentNode.appendChild(counter);

                    // Listen for input events
                    metaInput.addEventListener('input', updateCounter);

                    // Initialize
                    updateCounter();
                });
            </script>

            <?php
        }
        
    }
}
