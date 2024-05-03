<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;
 
class ViteSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       // Ensure Vite generates a CSP nonce
        Vite::useCspNonce();
        
        // Get the generated nonce value
        $nonce = Vite::cspNonce();

        // Define your CSP directives
        $cspDirectives = [
            'base-uri' => "'self'",
            'script-src' => "'self' kit.fontawesome.com 'nonce-$nonce' cdn.jsdelivr.net",
            'style-src' => "'self' 'nonce-$nonce' fonts.googleapis.com cdn.jsdelivr.net kit.fontawesome.com fonts.bunny.net cdnjs.cloudflare.com",
            'connect-src' => "'self' ka-f.fontawesome.com",
            // 'default-src' => "default-src 'self' fonts.gstatic.com cdn.jsdelivr.net",
            'form-action' => "'self'",
            'img-src' => "'self' data:",
            'media-src' => "'self'",
            'object-src' => "'self'",
            'frame-src' => "'self'",
            'frame-ancestors' => "'self'",
            'font-src' => "'self' fonts.gstatic.com fonts.googleapis.com *.fontawesome.com cdn.jsdelivr.net fonts.bunny.net",
            'manifest-src' => "'self'",
            // Add other directives as needed
        ];

        // Compile CSP header string
        $cspHeader = collect($cspDirectives)->map(function ($directives, $directiveName) {
            return "$directiveName $directives";
        })->implode('; ');

        // Add the CSP header to the response
        $response = $next($request);
        if ($response->isClientError() || $response->isServerError()) {
            $content = $response->getContent();
            $contentWithNonce = $this->addNonceToInlineScripts($content, $nonce);

            $response->setContent($contentWithNonce);
        } else {
            $response->headers->set('Content-Security-Policy', $cspHeader);
        }
        return $response;
    }

    protected function addNonceToInlineScripts($content, $nonce)
    {
        return preg_replace_callback('/<script(.*?)>(.*?)<\/script>/is', function ($match) use ($nonce) {
            $attributes = $match[1];
            $scriptContent = $match[2];

            if (!str_contains($attributes, 'nonce')) {
                $attributes .= " nonce=\"$nonce\"";
            }

            return "<script$attributes>$scriptContent</script>";
        }, $content);
    }
}