<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2014
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

class User_photo extends CI_Model {

    public function __construct() {

        parent::__construct();
    }

    /**
     * Gets the URL of a user's photo/avatar.
     * @param mixed     $user           The user ID or and array that contains user record with 'email' field at least.
     *                                  If user's record exist within the current programming context it is preferable
     *                                  the record to be passed for not quering the database again.
     * @param mixed     $_options       Array of non-mandatory options with these keys:
     *                                  'size' (of the squared photo), 'default_image', 'force_default_image', 'rating'.
     *                                  If $_options is integer, its value represents size.
     *                                  It is possible more options to be implemented in the future.
     * @return                          Returns the URL of user's photo.
     */
    public function get($user, $_options = null) {

        // These are the currently supported options for now.

        $size = null;
        $default_image = null;
        $force_default_image = null;
        $rating = null;

        // Read the options.

        if (is_array($_options)) {
            extract($_options, EXTR_IF_EXISTS);
        } elseif ($_options !== null) {
            $size = (int) $_options;
        }

        // Gather needed photo-related data about the user.

        $got_user_id = false;
        $got_data = false;

        $user_id = null;
        $email = null;

        if (is_array($user)) {

            if (array_key_exists('email', $user)) {

                $email = $user['email'];
                $got_data = true;

            } elseif (isset($user['id'])) {

                $user_id = (int) $user['id'];
                $got_user_id = true;
            }

        } elseif ($user !== null) {

            $user_id = (int) $user;
            $got_user_id = true;
        }

        if (!$got_data && $got_user_id) {

            $this->load->model('users');
            $user = $this->users->select('email')->with_deleted()->get($user_id);

            return $this->get($user, $_options);
        }

        // Build the result URL.

        $email = (string) $email;

        $this->load->library('gravatar');

        return $this->gravatar->get($email, $size, $default_image, $force_default_image, $rating);
    }

}
