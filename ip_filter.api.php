<?php

/**
 * @file
 * Documentation for ip_filter API.
 */

/**
 * Alter if current user IP is blocked/allowed.
 * 
 * @param array &$data
 *   An associative array containing current user IP address & status of it (Blocked/Allowed):
 *   - 'current_user_ip': The current user IP.
 *   - 'is_blocked': Boolean to state if the IP is blocked.
 * 
 */
function hook_ip_filter_allowed_ip_alter(array &$data) {
}
  