import { jsonSync } from "./utils";


/**
 * This file is part of the Kaze project.
 *
 * Copyright (c) 2021 Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Eki Prathama Ramdhani <eq.petrucci@gmail.com>
 * @file   AttGateSvc.js
 * @date   2021-07-30 1:59:44
 */

export function AttGateSvc(host, port, att_ip, att_port) {
	this.data = {
		ip: att_ip.toString(),
		port: att_port
	};

	this.host = host;
	this.port = port;

	this._access_token = null;
	this._request = _request.bind(this);

	this.auth = auth.bind(this);
	this.enroll = enroll.bind(this);
	this.add_user = add_user.bind(this);
	this.user_fp = user_fp.bind(this);
	this.accessToken = accessToken.bind(this);
}

function accessToken(token = null) {
	return token === null ? this._access_token : token;
}

function auth(username, password) {
	return this._request("POST", "auth", { username, password });
}

function enroll(user_id, finger_index) {
	return this._request("POST", "enroll", { user_id, finger_index });
}

function add_user(name) {
	return this._request("POST", "user/add", { name });
}

function user_fp(id) {
	return this._request("POST", "user/fp:" + id);
}

function _request(method, path, request_data) {
	let self = this;
	let host = this.host;
	let port = this.port;
	let headers = {
		"Accept": "application/json",
		"Content-Type": "application/json",
		// "Access-Control-Allow-Origin": "https://kaze.omnity.dev"
	};

	if (path !== "auth") {
		request_data = {
			...this.data,
			...request_data
		};

		headers = {
			...headers,
			"Authorization": "JWT " + this.accessToken()
		};
	}

	return (() => {
		let result = {
			result: null,
			error: null
		};

		jsonSync(
			method,
			"http://" + host + ":" + port + "/" + path,
			headers,
			true,
			request_data,
			(res) => {
				// safe access token for later
				if (path === "auth") self.accessToken(res.access_token);
				result.result = res;
			},
			(err) => {
				result.error = err;
			}
		);

		// wait for the request to be done and return the result
		return result;
	})();
}
