import React, { useEffect, useState } from 'react';

interface SplashScreenProps {
    onFinish: () => void;
}

export const SplashScreen: React.FC<SplashScreenProps> = ({ onFinish }) => {
    const [isVisible, setIsVisible] = useState(true);

    useEffect(() => {
        // Fallback timeout in case video doesn't play or is too long
        const timeout = setTimeout(() => {
            handleFinish();
        }, 5000); // 5 seconds max

        return () => clearTimeout(timeout);
    }, []);

    const handleFinish = () => {
        setIsVisible(false);
        setTimeout(onFinish, 500); // Wait for fade out animation
    };

    if (!isVisible) return null;

    return (
        <div
            className={`fixed inset-0 z-50 flex items-center justify-center bg-black transition-opacity duration-500 ${isVisible ? 'opacity-100' : 'opacity-0'}`}
        >
            <video
                autoPlay
                muted
                playsInline
                className="w-full h-full object-cover"
                onEnded={handleFinish}
            >
                <source src="/aiq44.mp4" type="video/mp4" />
                Your browser does not support the video tag.
            </video>
        </div>
    );
};
