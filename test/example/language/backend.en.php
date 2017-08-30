<?php
require_once realpath(__DIR__ . '/..').'/config.php';
$lang = [
    //core
    'core_register_success' => 'Process Register Successfully!',
    'core_register_failed' => 'Process Register Failed!',
    'core_update_success' => 'Process Update Successfully!',
    'core_update_failed' => 'Process Update Failed!',
    'core_delete_success' => 'Process Delete Successfully!',
    'core_delete_failed' => 'Process Delete Failed!',
    'core_upload_success' => 'Process Upload Successfully!',
    'core_upload_failed' => 'Process Upload Failed!',
    'core_login_failed' => 'Process Login Failed!',
    'core_not_connected' => 'Can not connected to the server!',
    'core_api_add_success' => 'Process Add new API Keys Successfully!',
    'core_api_add_failed' => 'Process Add new API Keys Failed!',
    'core_mail_reset_password1' => 'Request reset password',
    'core_mail_reset_password2' => 'You have already requested to reset password.',
    'core_mail_reset_password3' => 'Here is the link to reset:',
    'core_mail_reset_password4' => 'Just ignore this email if You don\'t want to reset password. Link will be expired 3days from now.',
    'core_mail_reset_password5' => 'Thank You',
    'core_reset_password_success1' => 'Request reset password hasbeen sent to your email!',
    'core_reset_password_success2' => 'If not, try to resend again later.',
    'core_reset_password_failed' => 'Process Forgot Password Failed!',
    'core_change_password_success' => 'Process Change Password Successfully!',
    'core_change_password_failed' => 'Process Change Password Failed!',
    'core_mail_send_success' => 'The message is successfully sent!',
    'core_mail_send_failed' => 'The message is failed to sent!',
    'core_try_again' => 'Please try again later!',
    'core_settings_changed' => 'Settings hasbeen changed!',
    'core_auto_refresh' => 'This page will automatically refresh at 2 seconds...',
    //login
    'login' => 'Login',
    'form_login' => 'Form Login',
    //register
    'register' => 'Register',
    'form_register' => 'Form Register',
    //contact
    'contact_us' => 'Contact Us',
    'form_contact_us' => 'Form Contact Us',
    'send_message_failed' => 'Process Send Message Failed,',
    'wrong_security_key' => 'Wrong security key!',
    'send_message' => 'Send Message',
    //forgot password
    'reset_password' => 'Request Reset Password',
    'form_reset_password' => 'Form Request Reset Password',
    'submit_reset_password' => 'Reset Password',
    //verify password
    'verify_password' => 'Verify New Password',
    'form_verify_password' => 'Form Verify New Password',
    //dashboard
    'dashboard' => 'Dashboard',
    //api key
    'api_key' => 'API Key',
    'api_keys' => 'API Keys',
    //data user, edit, view profile
    'edit_user_profile' => 'Edit User Profile',
    'user_profile' => 'User Profile',
    'my_profile' => 'My Profile',
    'view_profile' => 'View Profile',
    //explore file
    'explore_file' => 'Explore File',
    'upload_file' => 'Upload File to Server',
    'upload_now' => 'Upload Now',
    'detail_file' => 'Detail File',
    'upload_here' => 'Upload files here...',
    //settings
    'settings' => 'Settings',
    'no_have_api' => 'Doesn\'t have any API Keys? You can create at least one API Key at',
    //general
    'home' => 'Home',
    'username' => 'Username',
    'password' => 'Password',
    'confirm_password' => 'Confirm Password',
    'new_password' => 'New Password',
    'confirm_new_password' => 'Confirm New Password',
    'change_password' => 'Change Password',
    'not_match_password' => 'Your password is not match!',
    'fullname' => 'Fullname',
    'address' => 'Address',
    'phone' => 'Phone',
    'about_me' => 'About Me',
    'avatar' => 'Avatar',
    'security_key' => 'Security Key:',
    'name' => 'Name',
    'email_address' => 'Email Address',
    'subject' => 'Subject',
    'message' => 'Message',
    'remember_me' => 'Remember Me',
    'forgot_password' => 'Forgot Password?',
    'terms' => 'Terms Of Service',
    'i_agree' => 'I agree to the',
    'not_agree' => 'You are not agree to the terms of service!',
    'close' => 'Close',
    'submit' => 'Submit',
    'search' => 'Search',
    'add' => 'Add new',
    'cancel' => 'Cancel',
    'edit' => 'Edit',
    'manage' => 'Manage',
    'delete' => 'Delete',
    'domain' => 'Domain',
    'data' => 'Data',
    'shows_no' => 'Shows no:',
    'from_total_data' => 'from total data:',
    'export' => 'Export',
    'status' => 'Status',
    'update' => 'Update',
    'user' => 'User',
    'profile' => 'Profile',
    'registered' => 'Registered',
    'last_updated' => 'Last Updated',
    'title' => 'Title',
    'alternate' => 'Alternate',
    'external_link' => 'External Link',
    'browse_file' => 'Browse File',
    'show_detail' => 'Show Detail',
    'base_path' => 'Base Path',
    'url_api' => 'Url API',
    'save' => 'Save',
    'info' => 'Info',
    'not_found' => 'Not Found',
    'logout' => 'Logout',
    'explore' => 'Explore',
    'here' => 'here',
    //general table
    'tb_no' => 'No',
    'tb_item_id' => 'Item ID',
    'tb_role' => 'Role',
    'tb_username' => 'Username',
    'tb_created_at' => 'Created',
    'tb_updated_at' => 'Updated at',
    'tb_updated_by' => 'Updated by',
    'tb_date_upload' => 'Date Uploaded',
    'tb_upload_by' => 'Uploaded by',
    'tb_file_type' => 'File Type',
    'tb_direct_link' => 'Direct Link',
    //general input form
    'input_username' => 'Your Username',
    'input_password' => 'Your Password',
    'input_name' => 'Your Name here...',
    'input_email' => 'Your Email Address here...',
    'input_subject' => 'Please input the subject here...',
    'input_message' => 'Please input the message here...',
    'input_security_key' => 'Answer this question here...',
    'input_confirm_password' => 'Repeat your Password',
    'input_fullname' => 'Your Fullname here...',
    'input_address' => 'Your Address here...',
    'input_phone' => 'Your Phone here...',
    'input_about_me' => 'Here can be your description...',
    'input_avatar' => 'Please input url image for Your Avatar.',
    'input_search' => 'Search here...',
    'input_domain' => 'Your domain here...',
    'input_api_key' => 'Your API Key here...',
    'input_title_file' => 'Title name of your file...',
    'input_title_website' => 'Please input the title of your website...',
    'input_alternate_file' => 'Alternate name of your file...',
    'input_external_link' => 'Url to the external link...',
    'input_choose_file' => 'Choose File',
    'input_item_id' => 'The Item ID of your file...',
    'input_date_upload' => 'The Date Upload of your file...',
    'input_upload_by' => 'The username of the uploader...',
    'input_file_type' => 'The Type of your file...',
    'input_direct_link' => 'The url direct link of your file...',
    'input_base_path' => 'Please input url folder of Your website...',
    'input_url_api' => 'Please input url folder of Your Rest API...',
    //general modal
    'modal_terms' => '<p>You agree, through your use of this service, that you will not use this
    application to post any material which is knowingly false and/or defamatory,
    inaccurate, abusive, vulgar, hateful, harassing, obscene, profane, sexually
    oriented, threatening, invasive of a person\'s privacy, or otherwise violative
    of any law. You agree not to post any copyrighted material unless the
    copyright is owned by you.</p>
    
    <p>We as owner of this application also reserve the right to reveal your identity (or
    whatever information we know about you) in the event of a complaint or legal
    action arising from any message posted by you. We log all internet protocol
    addresses accessing this web site.</p>
    
    <p>Please note that advertisements, chain letters, pyramid schemes, and
    solicitations are inappropriate on this application.</p>
    
    <p>We reserve the right to remove any content for any reason or no reason at
    all. We reserve the right to terminate any membership for any reason or no
    reason at all.</p>
    
    <p>You must be at least 13 years of age to use this service.</p>'
];