<?php
/**
 * Sanitizes a string from user input or from the database and upgrade as a USERNAME
 * 
 * @since 5.0.6
 * 
 * @return string|empty
 */
function sanitize_username( $str ) {
	$username = strtolower( $str ); // lower case alphabet

	// convert everything into hyphen other then alphabet, number, and hyphen
	$username = preg_replace('/[^a-z0-9\-]/', '', $username);

	// convert multiple hypen into single
	$username = preg_replace( '|-+|', '-', $username );

	// trim for leading and trailing periods and whitespace.
    $username = trim( $username, " \t\n\r\0\x0B." );
	// trim hyphen
	$username = trim($username, "-");

	return $username;
}

/**
 * Strips out all characters that are not allowable in an email.
 *
 * @since 5.0.6
 *
 * @param string $email Email address to filter.
 * 
 * @return string Filtered email address.
 */
function sanitize_email( $email ) {
	// Test for the minimum length the email can be.
	if ( strlen( $email ) < 6 ) {
		return '';
	}

	// Test for an @ character after the first position.
    if ( strpos( $email, '@', 1 ) === false ) {
        return '';
    }

	// Split out the local and domain parts.
    list( $local, $domain ) = explode( '@', $email, 2 );

	// LOCAL PART
    // Test for invalid characters.
    $local = preg_replace( '/[^a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]/', '', $local );
    if ( '' === $local ) {
        return '';
    }

	// DOMAIN PART
    // Test for sequences of periods.
    $domain = preg_replace( '/\.{2,}/', '', $domain );
    if ( '' === $domain ) {
        return '';
    }
 
    // Test for leading and trailing periods and whitespace.
    $domain = trim( $domain, " \t\n\r\0\x0B." );
    if ( '' === $domain ) {
        return '';
    }

	// Split the domain into subs.
    $subs = explode( '.', $domain );
 
    // Assume the domain will have at least two subs.
    if ( 2 > count( $subs ) ) {
        return "";
    }

	// Create an array that will contain valid subs.
    $new_subs = array();
 
    // Loop through each sub.
    foreach ( $subs as $sub ) {
        // Test for leading and trailing hyphens.
        $sub = trim( $sub, " \t\n\r\0\x0B-" );
 
        // Test for invalid characters.
        $sub = preg_replace( '/[^a-z0-9-]+/i', '', $sub );
 
        // If there's anything left, add it to the valid subs.
        if ( '' !== $sub ) {
            $new_subs[] = $sub;
        }
    }
 
    // If there aren't 2 or more valid subs.
    if ( 2 > count( $new_subs ) ) {
        return "";
    }
 
    // Join valid subs into the new domain.
    $domain = implode( '.', $new_subs );

	// Put the email back together.
    $sanitized_email = $local . '@' . $domain;

	return $sanitized_email;
}

/**
 * Sanitize numeric value from text
 * 
 * @since 5.0.0
 */
function sanitize_number( $text ) {
	$output = strip_tags( trim( $text ) );

	return (int) $output;
}

