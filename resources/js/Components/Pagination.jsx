import React from 'react';
import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    if (links.length <= 3) return null;

    return (
        <div className="flex flex-wrap justify-center gap-2 mt-8 px-4">
            {links.map((link, key) => (
                link.url === null ? (
                    <div
                        key={key}
                        className="px-4 py-2 text-sm font-bold text-gray-400 bg-white border border-gray-100 rounded-xl cursor-default"
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                ) : (
                    <Link
                        key={key}
                        href={link.url}
                        className={`px-4 py-2 text-sm font-bold rounded-xl border transition-all ${
                            link.active
                                ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20'
                                : 'bg-white text-gray-600 border-gray-100 hover:border-blue-500 hover:text-blue-600'
                        }`}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                )
            ))}
        </div>
    );
}
