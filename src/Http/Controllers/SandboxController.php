<?php

namespace Vendor\LaravelMeta\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\LaravelMeta\Core\MetaClient;
use Vendor\LaravelMeta\Core\TokenManager;
use Exception;

class SandboxController extends Controller
{
    public function index()
    {
        return view('meta::sandbox');
    }

    public function testToken(Request $request)
    {
        $token = $request->input('token') ?: config('meta.token');

        if (!$token) {
            return back()->with('error', 'Please provide a System User Token or set it in your .env file.');
        }

        try {
            $client = new MetaClient($token);
            $response = $client->get('/me', ['fields' => 'id,name']);

            $debugInfo = null;
            $appId = config('meta.app.id');
            if ($appId) {
                $tokenManager = new TokenManager($client);
                $debugInfo = $tokenManager->debugToken($token);
            }

            return back()->with('success', 'Token is valid!')
                         ->with('token_data', [
                             'me' => $response,
                             'debug' => $debugInfo
                         ]);
        } catch (Exception $e) {
            return back()->with('error', 'Token validation failed: ' . $e->getMessage());
        }
    }

    public function getLead(Request $request)
    {
        $token = $request->input('token') ?: config('meta.token');
        $leadId = $request->input('lead_id');

        if (!$token || !$leadId) {
            return back()->with('error', 'Token and Lead ID are required.');
        }

        try {
            $client = new MetaClient($token);
            // Simulate the Leads module (since it's not fully built yet in Phase 3)
            $response = $client->get('/' . $leadId);
            return back()->with('success', 'Lead fetched successfully!')
                         ->with('api_response', $response);
        } catch (Exception $e) {
            return back()->with('error', 'API Request failed: ' . $e->getMessage());
        }
    }

    public function publishFacebook(Request $request)
    {
        $token = $request->input('token') ?: config('meta.token');
        $pageId = $request->input('page_id');
        $message = $request->input('message');

        if (!$token || !$pageId || !$message) {
            return back()->with('error', 'Token, Page ID, and Message are required.');
        }

        try {
            $client = new MetaClient($token);
            // Simulate the Facebook module
            $response = $client->post('/' . $pageId . '/feed', [
                'message' => $message
            ]);
            return back()->with('success', 'Posted to Facebook successfully!')
                         ->with('api_response', $response);
        } catch (Exception $e) {
            return back()->with('error', 'API Request failed: ' . $e->getMessage());
        }
    }
}
