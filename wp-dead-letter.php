<?php
/*
Plugin Name: Dead-Letter.Email
Plugin URI:  https://www.dead-letter.email
Description: Dead simple disposable email check that just works.
Version:     0.0.5
Author:      SoftCreatR Media
Author URI:  https://www.softcreatr.com
Text Domain: dead-letter-email
Domain Path: /languages
License:     LGPLv2.1+

This library is free software; you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation; either version 2.1 of the License, or
(at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with this library; if not, write to the
Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// Prevent direct access to this file
if (!function_exists('add_filter')) {
    http_response_code(403);
    exit;
}

/**
 * Dead-Letter.Email main class.
 *
 * @copyright  2019 SoftCreatR Media
 * @license    GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package    Wordpress\Dead-Letter
 */
final class DeadLetter
{
    /**
     * URL of our disposable email address database.
     * Using the flat JSON version for performance.
     *
     * @var string
     */
    const BLACKLIST_URL = 'https://www.dead-letter.email/blacklist_flat.json';
    
    /*
     * Whether the given email address is disposable or not
     *
     * @var boolean
     */
    public $isDisposable = false;
    
    /*
     * The domain blacklist
     *
     * @var string[]
     */
    public $blacklist = array();
    
    /**
     * Creates new instance of DeadLetter.
     *
     * @return void
     */
    public function __construct()
    {
        // Load locales
        add_action('init', function() {
            load_plugin_textdomain('dead-letter-email', false, dirname(plugin_basename(__FILE__)) . '/languages');
        });
        
        // Load the blacklist
        $this->getDB();
        
        // Add several filters
        add_filter('is_email', array($this, 'check'));
        add_filter('registration_errors', array($this, 'displayError'));
        add_filter('user_profile_update_errors', array($this, 'displayError'));
        add_filter('login_errors', array($this, 'displayError'));
        add_filter('settings_errors', array($this, 'displayError'));
    }
    
    /**
     * Nobody's perfect. So log any errors and blame me later ;)
     *
     * @param  mixed
     * @return void
     */
    public function doLog($message)
    {
        if (!WP_DEBUG_LOG) {
            // Phew, lucky me. No log for you ;)
            return;
        }
        
        error_log('[Dead-Letter]: ' . $message);
    }
    
    /**
     * Loads the local DLE database.
     * Also checks the last update time and updates it, if outdated.
     * The database is assumed to be outdated, if it's older than 60 * 60 * 24 (24 hours).
     *
     * @return void
     */
    public function getDB()
    {
        $dbFilename = __DIR__ . '/' . basename(self::BLACKLIST_URL);
        
        // Update database, if required
        if (!file_exists($dbFilename) || time() - filemtime($dbFilename) > 60 * 60 * 24) {
            try {
                $response = wp_remote_get(self::BLACKLIST_URL);
                $responseCode = wp_remote_retrieve_response_code($response);
                
                if ($responseCode !== 200) {
                    throw new Requests_Exception('Cannot download DLE database.', 'nocontent');
                }
                
                $data = wp_remote_retrieve_body($response);
                
                // Save blacklist to file
                @file_put_contents($dbFilename, $data);
                
                // Make our blacklist accessible for later use
                $this->blacklist = json_decode($data, true);
            } catch (Exception $e) {
                // Looks like we were unable to download the database for some reason
                $this->doLog($e->getMessage());
                
                @unlink($dbFilename);
                return;
            }
        } else {
            $dbFileContent = @file_get_contents($dbFilename);
            
            // Something seems to be wrong, remove file and disable check for now
            if (empty($dbFileContent)) {
                $this->doLog('DLE database is empty.');
                
                @unlink($dbFilename);
                return;
            }
            
            // Make our blacklist accessible for later use
            $this->blacklist = json_decode($dbFileContent, true);
        }
    }
    
    /**
     * Performs several actions/checks on a given email address.
     *
     * @param  $email
     * @return void
     */
    public function check($email)
    {
        $domain = $this->getDomain($email);
        
        // No (valid) domain extracted from email address
        if (empty($domain)) {
            $this->doLog('Invalid email address (cannot extract domain).');
            
            return false;
        }
        
        return $this->isDisposable($domain) ? false : true;
    }
    
    /**
     * Displays a meaningful error message.
     *
     * @return boolean
     */
    public function displayError($errors)
    {
        if ($this->isDisposable) {
            $message = __('The usage of disposable email addresses is not allowed.', 'dead-letter-email');
            
            if ($errors instanceof WP_Error) {
                $errors->add('disposable_email', $message);
            } elseif (is_string($errors)) {
                $errors .= '<br />' . $message;
            }
        }
        
        return $errors;
    }
    
    /**
     * Performs several checks on a given email address.
     *
     * @param  $email
     * @return string
     */
    private function getDomain($email)
    {
        return trim(mb_strtolower(substr($email, strrpos($email, '@') + 1)));
    }
    
    /**
     * Checks, if a given domain exists in the local DLE database.
     *
     * @param  $domain
     * @return boolean
     */
    private function isDisposable($domain)
    {
        // The database only provides double sha1 hashed domain representations
        $domainHash = sha1(sha1($domain));
        
        // check against local database
        $flipped = array_flip($this->blacklist);
        $this->isDisposable = array_key_exists($domainHash, $flipped);
        
        return $this->isDisposable;
    }
}

// Call our class
(new DeadLetter());
