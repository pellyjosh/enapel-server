import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';

// Detect if running inside Electron (NativePHP native app).
// In Electron, the user agent always contains the string "Electron".
const isElectron = typeof navigator !== 'undefined' &&
    navigator.userAgent.toLowerCase().includes('electron');

const PageTransition = ({ children, url }) => {
    // Completely disable transitions in Electron to prevent white screen flashes.
    if (isElectron) {
        return children;
    }

    // On the web, keep the smooth crossfade transition.
    return (
        <AnimatePresence>
            <motion.div
                key={url}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.18, ease: 'easeInOut' }}
                className="flex-1 flex flex-col min-h-0 w-full"
            >
                {children}
            </motion.div>
        </AnimatePresence>
    );
};

export default PageTransition;
