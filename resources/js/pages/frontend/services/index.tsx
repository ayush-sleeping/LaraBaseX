// resources/js/pages/frontend/contact.tsx
import { Navbar } from '@/components/Frontend/navbar';

export default function ServicesPage() {
    // Data for the service cards
    const services = [
        {
            title: 'Web Development',
            image: 'https://framerusercontent.com/images/PGqhbyNizzg0WF0Ff8Ct1xJCz4.png?scale-down-to=512',
            overlayImage: 'https://framerusercontent.com/images/R8KAWJ8XJ7xyTu7ucAu7MwYY.png?scale-down-to=512',
        },
        {
            title: 'Creative Design',
            image: 'https://framerusercontent.com/images/icQGsV71x2rSlISc1VdMnw1qP0.png?scale-down-to=512',
            overlayImage: 'https://framerusercontent.com/images/lXJpgpSzhcdgjAHyzQ8gL6xZio.png?scale-down-to=512',
        },
        {
            title: 'Branding',
            image: 'https://framerusercontent.com/images/fDuEIn62K1IFn5Ej7fSyTMA71og.png?scale-down-to=512',
            overlayImage: 'https://framerusercontent.com/images/swGfymsPbpYnmJh0xWYUDsjYEVw.png?scale-down-to=512',
        },
        {
            title: 'Product Design',
            image: 'https://framerusercontent.com/images/fTivRAMCNvUFDAp9M0oddRMjk.png?scale-down-to=512',
            overlayImage: 'https://framerusercontent.com/images/ykQMkxdWQtCI1O7dEHnQs9vQmME.png?scale-down-to=512',
        },
    ];
    return (
        <div className="relative min-h-screen w-full bg-white">
            <Navbar />
            <style>
                {`
              @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');
              body {
                font-family: 'Inter', sans-serif;
              }
            `}
            </style>
            <div className="container mx-auto px-4 py-16">
                <div className="mx-auto max-w-5xl">
                    {/* Section Header */}
                    <div className="mb-12 text-center sm:mb-16">
                        <h2 className="mb-3 text-4xl font-bold tracking-tight text-gray-900 sm:mb-4 sm:text-5xl lg:text-6xl dark:text-white">
                            How Can I Help?
                        </h2>
                        <p className="text-lg font-light text-gray-500 sm:text-xl dark:text-gray-400">
                            Let's turn your vision into something amazing.
                        </p>
                    </div>

                    {/* Services Grid */}
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:gap-8">
                        {services.map((service, index) => (
                            <div
                                key={index}
                                className="group flex h-[320px] flex-col rounded-3xl bg-gray-50 p-6 transition-all duration-300 hover:bg-gray-100 dark:bg-gray-800/50 dark:hover:bg-gray-800"
                            >
                                {/* Image Container */}
                                <div className="relative mb-4 flex flex-grow items-center justify-center">
                                    {/* Back Image */}
                                    <img
                                        src={service.image}
                                        alt={`${service.title} showcase`}
                                        className="absolute h-auto w-44 -rotate-6 transform rounded-lg shadow-md transition-all duration-400 ease-in-out group-hover:scale-105 group-hover:rotate-[-10deg]"
                                        onError={(e) => {
                                            const target = e.target as HTMLImageElement;
                                            target.onerror = null;
                                            target.src = 'https://placehold.co/512x512/e2e8f0/4a5568?text=Image+1';
                                        }}
                                    />
                                    {/* Front Image */}
                                    <img
                                        src={service.overlayImage}
                                        alt={`${service.title} example`}
                                        className="absolute h-auto w-44 rotate-3 transform rounded-lg shadow-lg transition-all duration-400 ease-in-out group-hover:scale-105 group-hover:rotate-[5deg]"
                                        onError={(e) => {
                                            const target = e.target as HTMLImageElement;
                                            target.onerror = null;
                                            target.src = 'https://placehold.co/512x512/cbd5e0/2d3748?text=Image+2';
                                        }}
                                    />
                                </div>

                                {/* Service Title */}
                                <h3 className="mt-auto text-left text-lg font-medium text-gray-800 dark:text-gray-100">{service.title}</h3>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
