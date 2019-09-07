<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-07-29
 * Time: 10:01
 */

namespace App\HttpController\Jwt;


use App\HttpController\Base;
use EasySwoole\Component\Singleton;
use EasySwoole\Http\Message\Message;
use EasySwoole\Http\Request;
use Firebase\JWT\JWT;

class AccessToken
{
	use Singleton;

	const iss             = 'test.whqrlm.com';
	const expire_interval = 7 * 24 * 60 * 60;
	const alg             = 'HS256';
	const key             = 'S0&sK#xo';
	protected $config;
	// 错误信息
	private $message;
	private $request;
	public function __construct()
	{
		$this->config = [
			'iss'             => self::iss,
			'alg'             => self::alg,
			'key'             => self::key,
			'expire_interval' => self::expire_interval,
		];
	}

	public function getRequest(Request $request)
	{
		return $this->request;
	}


	public function accessTokenModel()
	{
		return new \App\Models\AccessToken();
	}

	/**
	 * 创建AccessToken
	 * @param string $user_id
	 * @param int $start_time
	 * @return array|null
	 */
	public function createAccessToken(string $user_id, int $start_time ) : ? array
	{
		$jti = $this->accessTokenModel()->addAccessToken( [
			'sub' => $user_id,
			'iat' => $start_time,
			'exp' => $start_time + $this->config['expire_interval'],
		] );

		$access_token = $this->encode( [
			'jti' => $jti,
			'iss' => $this->config['iss'],
			'sub' => $user_id,
			'iat' => $start_time,
			'exp' => $start_time + $this->config['expire_interval'],
		]);

		return [
			'access_token' => $access_token,
			'expires_in'   => $this->config['expire_interval'],
		];
	}

	public function checkAccessToken( string $access_token ) : bool
	{
		if( !empty( $access_token ) ){
			$jwt = $this->decode( $access_token );
			if( empty( $jwt ) || !isset( $jwt['jti'] ) ){
				return false;
			}
			$find = $this->accessTokenModel()->getAccessTokenInfo( [
				'jti'        => $jwt['jti'],
				'sub'        => $jwt['sub'],
				'exp'        => $jwt['exp'],
				'is_invalid' => 0,
				'is_logout'  => 0,
			] );

			if( $find ){
				return true;
			} else{
				return false;
			}
		} else{
			return false;
		}
	}

	public function getRequestTokenString(array $header)
	{
		if( isset( $header['authorization'] ) && $token = str_replace( "Bearer ", "", $header['authorization'] ) ){
			return $token;
		} else{
			$this->send    = - 1;
			$this->sendMsg = 'authorization miss';
			return '';
		}
	}

	public function encode( array $data ) : string
	{
		return JWT::encode( $data, $this->config['key'], $this->config['alg'] );
	}

	public function decode( string $access_token ) : ?  array
	{
		try{
			$access_token_data = (array)JWT::decode( $access_token, $this->config['key'], [$this->config['alg']] );
		} catch( \Firebase\JWT\ExpiredException $e ){
			$this->message = $e->getMessage();
			return null;
		} catch( \Firebase\JWT\SignatureInvalidException $e ){
			$this->message = $e->getMessage();
			return null;
		} catch( \Exception $e ){
			$this->message = $e->getMessage();
			return null;
		}
		return $access_token_data;
	}

	public function getMessage(){
		return $this->message;
	}

	/**
	 * 刷新AccessToken
	 * @param string $access_token
	 * @param int $start_time
	 * @return array|null
	 */
	public function refreshAccessToken( string $access_token, int $start_time ) : ? array
	{
		$jwt = $this->decode( $access_token );

		$access_token_data = [
			'jti' => $jwt['jti'],
			'sub' => $jwt['sub'],
			'iat' => $start_time,
			'exp' => $start_time + $this->config['expire_interval'],
		];

		$refresh_access_token = $this->encode( $jwt );

		if( $refresh_access_token ){
			// 修改之前秘钥id的过期时间
			$state = $this->accessTokenModel()->editAccessToken( ['jti' => $access_token_data['jti']], [
				'exp' => $start_time + $this->config['expire_interval'],
			] );
			if( $state ){
				return [
					'access_token' => $refresh_access_token,
					'expires_in'   => $this->config['expire_interval'],
				];
			} else{
				return null;
			}
		} else{
			return null;
		}
	}
}