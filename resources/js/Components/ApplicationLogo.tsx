import { SVGAttributes } from 'react';

export default function ApplicationLogo(props: SVGAttributes<SVGElement>) {
    return (
      
        <svg {...props} xmlns="http://www.w3.org/2000/svg" width="480" height="120" viewBox="0 0 480 120" role="img" aria-labelledby="titleDesc">
        <title id="titleDesc">DevGuard logo</title>
        <desc id="descDesc">A shield icon with the word DevGuard</desc>

        <g transform="translate(24,12)">
            <path d="M48 0C48 0 96 12 96 48C96 84 64 108 48 120C32 108 0 84 0 48C0 12 48 0 48 0Z"
                fill="#6C5CE7"/>
        
            <path d="M48 8c23 0 48 9 48 40 0 28-22 49-48 60-26-11-48-32-48-60 0-31 25-40 48-40z"
                fill="#7F6EF8" opacity="0.9"/>
        
            <circle cx="48" cy="52" r="6" fill="#fff"/>
            <rect x="44" y="60" width="8" height="18" rx="3" fill="#fff"/>
        </g>


        <g transform="translate(140,72)" font-family="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial" >
            <text x="0" y="0" font-size="120" font-weight="700" fill="#ebebf4ff">Dev</text>
            <text x="72" y="0" font-size="120" font-weight="700" fill="#301adaff">Guard</text>
        </g>

        <rect x="140" y="84" width="240" height="2" rx="1" fill="#c3c1edff" opacity="0.6"/>
        </svg>

    );
}
