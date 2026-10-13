import React from 'react';

/**
 * VerifiedBadge Component
 * Universal blue scalloped starburst rosette with checkmark matching the Home page verified icon.
 */
export default function VerifiedBadge({
    size = 'sm', // 'xs' (13px) | 'sm' (15px) | 'md' (17px) | 'lg' (20px)
    className = '',
    title = 'Verified Identity'
}) {
    const sizeMap = {
        xs: 'w-3.5 h-3.5 min-w-[14px]',
        sm: 'w-4 h-4 min-w-[16px]',
        md: 'w-[18px] h-[18px] min-w-[18px]',
        lg: 'w-5 h-5 min-w-[20px]',
    };

    const sizeClass = sizeMap[size] || sizeMap.sm;

    return (
        <span 
            className={`inline-flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-500 select-none ${className}`}
            title={title}
            aria-label={title}
        >
            <svg 
                viewBox="0 0 20 20" 
                fill="currentColor" 
                className={`${sizeClass} fill-current shrink-0`}
            >
                <path 
                    fillRule="evenodd" 
                    clipRule="evenodd" 
                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 011.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" 
                />
            </svg>
        </span>
    );
}
