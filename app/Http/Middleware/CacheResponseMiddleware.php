<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $minutes  Cache duration in minutes (default: 30)
     */
    public function handle(Request $request, Closure $next, int $minutes = 30): Response
    {
        // Only cache GET requests
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated user requests or admin pages
        if ($request->user() || $request->is('admin/*') || $request->is('dashboard*')) {
            return $next($request);
        }

        // Generate cache key based on URL and query parameters
        $cacheKey = $this->generateCacheKey($request);

        // Try to get cached response
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            // Return cached response with cache headers
            $response = response($cachedResponse['content'], $cachedResponse['status'])
                ->withHeaders($cachedResponse['headers']);
            $response->headers->set('X-Cache-Status', 'HIT');
            $response->headers->set('X-Cache-Key', $cacheKey);

            return $response;
        }

        // Process the request
        $response = $next($request);

        // Only cache successful responses
        if ($response->getStatusCode() === 200 && $this->shouldCacheResponse($request, $response)) {
            $cacheData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $this->getCacheableHeaders($response),
            ];

            Cache::put($cacheKey, $cacheData, now()->addMinutes($minutes));

            $response->headers->set('X-Cache-Status', 'MISS');
            $response->headers->set('X-Cache-Key', $cacheKey);
            $response->headers->set('X-Cache-Duration', $minutes.' minutes');
        }

        return $response;
    }

    /**
     * Generate cache key for the request
     */
    private function generateCacheKey(Request $request): string
    {
        $url = $request->url();
        $queryString = $request->getQueryString();

        $key = 'response.'.md5($url.($queryString ? '?'.$queryString : ''));

        return $key;
    }

    /**
     * Determine if the response should be cached
     */
    private function shouldCacheResponse(Request $request, Response $response): bool
    {
        // Don't cache if response contains errors
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        // Don't cache if response is too large (> 1MB)
        if (strlen($response->getContent()) > 1024 * 1024) {
            return false;
        }

        // Don't cache if response contains dynamic content indicators
        $content = $response->getContent();
        $dynamicIndicators = ['csrf_token', 'session_token', '_token'];

        foreach ($dynamicIndicators as $indicator) {
            if (strpos($content, $indicator) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get cacheable headers from response
     */
    private function getCacheableHeaders(Response $response): array
    {
        $cacheableHeaders = [
            'Content-Type',
            'Content-Encoding',
            'Content-Language',
            'Cache-Control',
            'ETag',
            'Last-Modified',
        ];

        $headers = [];
        foreach ($cacheableHeaders as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }
}
