<?php
/**
 * About
 *
 * @package wp-fail2ban
 * @since 4.2.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * About content
 *
 * @since 4.2.0
 *
 * @param bool  $hide_title
 */
function about($hide_title = false)
{
    $wp_f2b_ver = substr(WP_FAIL2BAN_VER, 0, strrpos(WP_FAIL2BAN_VER, '.'));
    $notice = version_compare(PHP_VERSION, '5.6.0', '<');
?>
<div class="wrap">
  <style>
    div.inside ul {
      list-style: disc;
      padding-left: 2em;
    }
  </style>
<?php if (!$hide_title): ?>
  <h1>WP fail2ban</h1>
<?php endif; ?>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.8</h2>
            <div class="inside">
              <h4>News</h4>
              <ul>
<?php if (strtotime('1 May 2020') > time()): ?>
                <li><a href="https://forums.invis.net/?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="_blank"><strong>Support has moved</strong></a>. Please come join the new <em>WPf2b</em> community!</li>
<?php endif; ?>
                <li><a href="https://wp-fail2ban.com/blog/2020/04/16/testers-needed-v4-3-0-is-imminent/?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="_blank">Testers needed</a>! Version 4.3.0 will be released soon - I need your help to make sure it works properly.</li>
<?php if ($notice): ?>
                <li><strong>This is the last release that supports PHP <?php echo PHP_VERSION; ?></strong>.<br>The <strong>minimum version</strong> required for <strong>v4.3.0</strong> is <strong>PHP 5.6</strong>.<br>Please <a href="https://wordpress.org/support/update-php/" target="_blank"><strong>update your PHP</strong></a>.</li>
<?php endif; ?>
              </ul>
              <h4>Changes</h4>
              <ul>
                <li>Add link to new <a href="https://forums.invis.net/c/wp-fail2ban/?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="_blank">support forum</a>.</li>
                <li>Fix user enumeration conflict with Gutenberg (h/t @dinghy).</li>
                <li>Fix notices wrt admin menu (h/t @marioivangf).</li>
                <li>Fix harmless XDebug notice (h/t @dinghy).</li>
                <li>Update Freemius library.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.7.1</h2>
            <div class="inside">
              <ul>
                <li>Fix error when blocking user enumeration via <tt>oembed</tt>.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.7</h2>
            <div class="inside">
              <ul>
                <li>Fix error when blocking user enumeration via REST.</li>
                <li>Fix buttons on Settings tabs.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.6</h2>
            <div class="inside">
              <ul>
                <li>Add support for <a href="<?php echo admin_url('admin.php?page=wp-fail2ban-tools'); ?>">Remote Tools</a> add-on.
                <li>Add support for the new ClassicPress security page.</li>
                <li>Improved user enumeration blocking.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.5</h2>
            <div class="inside">
              <ul>
                <li>Properly fix PHP 5.3 support; tested on CentOS 6. Does not support any UI or Premium features.</li>
                <li>Fix potential issue with <tt>WP_FAIL2BAN_BLOCK_USER_ENUMERATION</tt> if calling REST API or XMLRPC from admin area.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.4</h2>
            <div class="inside">
              <ul>
                <li>Add filter for login failed message.</li>
                <li>Fix logging spam comments from admin area.</li>
                <li>Fix Settings link from Plugins page.</li>
                <li>Update Freemius library.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.3</h2>
            <div class="inside">
              <ul>
                <li>Workaround for some versions of PHP 7.x that would cause <tt>define()</tt>s to be ignored.</li>
                <li>Add config note to settings tabs.</li>
                <li>Fix documentation links.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.2</h2>
            <div class="inside">
              <ul>
                <li>Fix 5.3 compatibility.</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <h2>Version 4.2.1</h2>
            <div class="inside">
              <ul>
                <li>Completed support for <tt><a href="https://docs.wp-fail2ban.com/en/4.2/defines/WP_FAIL2BAN_COMMENT_EXTRA_LOG.html?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="docs.wp-fail2ban.com">WP_FAIL2BAN_COMMENT_EXTRA_LOG</a></tt>.</li>
                <li>Add support for 3rd-party plugins; see <a href="https://docs.wp-fail2ban.com/en/4.2/developers.html?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="docs.wp-fail2ban.com">Developers</a>.<br>
                  <p><ul>
                    <li>Add-on for <a href="https://wordpress.org/plugins/wp-fail2ban-addon-contact-form-7/">Contact Form 7</a> (experimental).</li>
                    <li>Add-on for <a href="https://wordpress.org/plugins/wp-fail2ban-addon-gravity-forms/">Gravity Forms</a> (experimental).</li>
                  </ul></p>
                </li>
                <li>Change logging for known-user with incorrect password; previously logged as unknown user and matched by <tt>hard</tt> filters (due to limitations in older versions of WordPress), now logged as known user and matched by <tt>soft</tt>.</li>
                <li>Bugfix for email-as-username - now logged correctly and matched by <tt>soft</tt>, not <tt>hard</tt>, filters.</li>
                <li>Bugfix for regression in code to prevent Free/Premium conflict.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div id="postbox-container-1" class="postbox-container">
        <div class="meta-box-sortables">
          <div class="postbox">
            <h2>Getting Started</h2>
            <div class="inside">
              <ol>
                <li><a href="https://docs.wp-fail2ban.com/en/<?=$wp_f2b_ver?>/introduction.html?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="docs.wp-fail2ban.com">Introduction</a></li>
                <li><a href="https://docs.wp-fail2ban.com/en/<?=$wp_f2b_ver?>/configuration.html?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="docs.wp-fail2ban.com">Configuration</a></li>
              </ol>
            </div>
          </div>
          <div class="postbox">
            <h2>Getting Help</h2>
            <div class="inside">
              <ul>
                <li><a href="<?=wf_fs()->apply_filters('support_forum_url', '')?>?utm_source=about&utm_medium=about&utm_campaign=4.2.8" target="_blank">Support Forum</a></li>
              </ul>
              <p><strong>Note:</strong> The WordPress.org forum is no longer used for support. Please do not ask questions there as they are likely to go unanswered.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    &nbsp;
  </div>
</div>
<?php
}

