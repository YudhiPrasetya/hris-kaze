<?php
/**
 * This file is part of the Omnity project.
 *
 * Copyright (c) 2020 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   HttpStatusCodes.php
 * @date   29/08/2020 03.33
 */

namespace App\Libraries;

class HttpStatusCodes {
	// [Informational 1xx]
	const HTTP_CONTINUE = 100;

	const HTTP_SWITCHING_PROTOCOLS = 101;

	// [Successful 2xx]
	const HTTP_OK = 200;

	const HTTP_CREATED = 201;

	const HTTP_ACCEPTED = 202;

	const HTTP_NONAUTHORITATIVE_INFORMATION = 203;

	const HTTP_NO_CONTENT = 204;

	const HTTP_RESET_CONTENT = 205;

	const HTTP_PARTIAL_CONTENT = 206;

	// [Redirection 3xx]
	const HTTP_MULTIPLE_CHOICES = 300;

	const HTTP_MOVED_PERMANENTLY = 301;

	const HTTP_FOUND = 302;

	const HTTP_SEE_OTHER = 303;

	const HTTP_NOT_MODIFIED = 304;

	const HTTP_USE_PROXY = 305;

	const HTTP_UNUSED = 306;

	const HTTP_TEMPORARY_REDIRECT = 307;

	// [Client Error 4xx]
	const errorCodesBeginAt = 400;

	const HTTP_BAD_REQUEST = 400;

	const HTTP_UNAUTHORIZED = 401;

	const HTTP_PAYMENT_REQUIRED = 402;

	const HTTP_FORBIDDEN = 403;

	const HTTP_NOT_FOUND = 404;

	const HTTP_METHOD_NOT_ALLOWED = 405;

	const HTTP_NOT_ACCEPTABLE = 406;

	const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

	const HTTP_REQUEST_TIMEOUT = 408;

	const HTTP_CONFLICT = 409;

	const HTTP_GONE = 410;

	const HTTP_LENGTH_REQUIRED = 411;

	const HTTP_PRECONDITION_FAILED = 412;

	const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;

	const HTTP_REQUEST_URI_TOO_LONG = 414;

	const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

	const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

	const HTTP_EXPECTATION_FAILED = 417;

	// [Server Error 5xx]
	const HTTP_INTERNAL_SERVER_ERROR = 500;

	const HTTP_NOT_IMPLEMENTED = 501;

	const HTTP_BAD_GATEWAY = 502;

	const HTTP_SERVICE_UNAVAILABLE = 503;

	const HTTP_GATEWAY_TIMEOUT = 504;

	const HTTP_VERSION_NOT_SUPPORTED = 505;

	private static $messages = array(
		// [Informational 1xx]
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Early Hints',
		122 => 'URI Too Long',

		// [Successful 2xx]
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',

		// [Redirection 3xx]
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => '(Unused)',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',

		// [Client Error 4xx]
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'am A Teapot',
		419 => 'Authentication Timeout',
		420 => 'Enhance Your Calm',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Too Early',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		430 => 'Request Header Fields Too Large',
		431 => 'Request Header Fields Too Large',
		440 => 'Login Time-out',
		444 => 'No Response',
		449 => 'Retry With',
		450 => 'Blocked by Windows Parental Controls',
		451 => 'Unavailable For Legal Reasons',
		494 => 'Request header too large',
		495 => 'SSL Certificate Error',
		496 => 'SSL Certificate Required',
		497 => 'HTTP Request Sent to HTTPS Port',
		498 => 'Invalid Token',
		499 => 'Token Required',

		// [Server Error 5xx]
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
		520 => 'Web Server Returned an Unknown Error',
		521 => 'Web Server Is Down',
		522 => 'Connection Timed Out',
		523 => 'Origin Is Unreachable',
		524 => 'A Timeout Occurred',
		525 => 'SSL Handshake Failed',
		526 => 'Invalid SSL Certificate',
		527 => 'Railgun Error',
		529 => 'Site is overloaded',
		530 => 'Site is frozen',
		598 => '(Informal convention) Network read timeout error',
	);

	private static $descriptions = [
		100 => 'Only a part of the request has been received by the server, but as long as it has not been rejected, the client should continue with the request. ',
		101 => 'The server switches protocol. ',
		102 => 'To avoid timing out, the server acknowledges that a request has been received and is being processed, though no response is available. ',
		103 => 'Resumes aborted PUT or POST requests in Resumable HTTP Requests Proposal. ',
		122 => 'An IE7-only code that indicates that the URI is longer than the maximum 2,083 characters. ',

		201 => 'The server has fulfilled the browser\'s request, and as a result, has created a new resource. ',
		202 => 'The server has accepted your browser\'s request but is still processing it. ',
		203 => 'The information in the entity header is from a local or third-party copy, not from the original server. ',
		204 => 'The server has successfully processed the request, but is not going to return any content. ',
		205 => 'The server has processed the request but is not going to return any content. ',
		206 => 'The server is returning partial data of the size requested. Used in response to a request specifying a Range header. The server must specify the range included in the response with the Content-Range header. ',
		207 => 'Depending on the number of sub-requests made by the client, the XML message that follows might contain multiple separate response codes. ',
		208 => 'The results have been included in a previous reply and are not being returned again. ',
		226 => 'A request for this resource has been fulfilled by the server. The response represents the result of one or more instance manipulations for the current instance. ',

		300 => 'A link list. The user can select a link and go to that location. Maximum five addresses. ',
		301 => 'The requested resource has been moved permanently. ',
		302 => 'The requested resource has moved, but was found. ',
		303 => 'The requested page can be found under a different url. ',
		304 => 'The requested resource has not been modified since the last time you accessed it. ',
		305 => 'The requested URL must be accessed through the proxy mentioned in the Location header. ',
		306 => 'This code was used in a previous version. It is no longer used, but the code is reserved. ',
		307 => 'The resource has been temporarily moved to a different URL. ',
		308 => 'This and all future requests should be repeated using a different URI as specified. Unlike 301 and 302, with 307 and 308 status codes the HTTP method should not change. ',

		400 => 'The server can\'t return a response due to an error on the client’s end. ',
		401 => 'The requested page needs a username and a password. ',
		402 => 'The request cannot be fulfilled, usually due to a lack of required funds. ',
		403 => 'Access to that resource is forbidden. ',
		404 => 'The server can not find the requested page. ',
		405 => 'The method specified in the request is not allowed. ',
		406 => 'The server can only generate a response that is not accepted by the client. ',
		407 => 'You must authenticate with a proxy server before this request can be served. ',
		408 => 'The request took longer than the server was prepared to wait. ',
		409 => 'The server couldn\'t process your browser’s request because there\'s a conflict with the relevant resource. ',
		410 => 'The requested resource is gone and won\'t be coming back. ',
		411 => 'The requested resource requires that the client did not specify a certain length. ',
		412 => 'Your browser included certain conditions in its request headers, and the server did not meet those specifications. ',
		413 => 'Your request is larger than the server is willing or able to process. ',
		414 => 'The URI is too large for the server to process. ',
		415 => 'The request includes a media type that the server or resource doesn\'t support. ',
		416 => 'Your request was for a portion of a resource that the server is unable to return. ',
		417 => 'The server is unable to meet the requirements specified in the request\'s expect header field. ',
		418 => 'The RFC specifies that this code should be returned by teapots requested to brew coffee. ',
		419 => 'Previously valid authentication has expired. ',
		421 => 'The request was directed at a server that is not able to produce a response (for example because of connection reuse). ',
		422 => 'The client request contains semantic errors, and the server can\'t process it. ',
		423 => 'Indicates that the resource that is being accessed is locked. ',
		424 => 'Indicates that the request failed because of the failure of a previous request. ',
		425 => 'The server is unwilling to process a request because it may be replayed. ',
		426 => 'The client should switch to a different protocol such as TLS/1.0, specified in the Upgrade header field. ',
		428 => 'The server requires conditions to be specified before processing the request. ',
		429 => 'You have sent too many requests in a given amount of time (rate-limiting). ',
		431 => 'The server can\'t process the request because the header fields are too large. ',
		440 => 'Microsoft extension indicating that the session has expired. ',
		444 => 'The server returned no information and closed the connection. ',
		449 => 'The request should be retried after performing a specific action. ',
		451 => 'The resource is not available due to legal reasons. ',
		495 => 'SSL client certificate error has occurred. ',
		496 => 'The client didn\'t provide certificate. ',
		497 => 'Plain HTTP requests were sent to HTTPS port. ',
		498 => 'Token is expired or otherwise invalid. ',
		499 => 'The connection has been closed by the client while the server is still processing its request, in which case the server is unable to send a status code back. ',

		500 => 'There was an error on the server and the request could not be completed. ',
		501 => 'The request was not completed. The server did not support the functionality required. ',
		502 => 'The request was not completed. The server received an invalid response from the upstream server. ',
		503 => 'The server is unavailable to handle this request right now. ',
		504 => 'The server, acting as a gateway, timed out waiting for another server to respond. ',
		505 => 'The server doesn\'t support the HTTP version the client used to make the request. ',
		506 => 'Indicates that transparent content negotiation for the request is causing a circular reference. ',
		507 => 'The server cannot store the representation necessary for completing the request. ',
		508 => 'The server detected an infinite loop while processing the request. ',
		509 => 'Use unknown. Status code not specified by any RFCs. ',
		510 => 'The server requires further extensions in order to fulfill the request. ',
		511 => 'Client must authenticate in order to gain network access. Often used by proxies that control network access such as Wi-Fi hotspots. ',
		598 => 'A network read time-out behind the proxy to a client in front of the proxy. ',
		599 => 'A network connect time-out behind the proxy to a client in front of the proxy. ',
	];

	public static function httpHeaderFor($code) {
		return 'HTTP/1.1 ' . self::$messages[$code];
	}

	public static function getDescription($code) {
		return self::$descriptions[$code] ??
		       (self::getMessage($code) ?? 'No description available for this kind of error.');
	}

	public static function getMessage($code) {
		return self::$messages[$code] ?? 'Unknown Error';
	}

	public static function isError($code) {
		return is_numeric($code) && $code >= self::HTTP_BAD_REQUEST;
	}

	public static function canHaveBody($code) {
		return
			// True if not in 100s
			($code < self::HTTP_CONTINUE || $code >= self::HTTP_OK)
			&& // and not 204 NO CONTENT
			$code != self::HTTP_NO_CONTENT
			&& // and not 304 NOT MODIFIED
			$code != self::HTTP_NOT_MODIFIED;
	}
}