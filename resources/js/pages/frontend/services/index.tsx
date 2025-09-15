// resources/js/pages/frontend/contact.tsx
import { Navbar } from '@/components/Frontend/navbar';

export default function ServicesPage() {
    // Data for the service cards
    const services = [
        {
            title: 'Full-Stack Development',
            description: 'Complete web applications built with Laravel backend and React frontend, featuring modern architecture and best practices.',
            icon: '⚡',
        },
        {
            title: 'API Development',
            description: 'Robust RESTful APIs with authentication, rate limiting, and comprehensive documentation for seamless integration.',
            icon: '🔗',
        },
        {
            title: 'Admin Dashboard',
            description: 'Powerful admin panels with role-based permissions, user management, and comprehensive data visualization tools.',
            icon: '📊',
        },
        {
            title: 'Authentication System',
            description: 'Secure user authentication with JWT tokens, email verification, password reset, and social login integration.',
            icon: '🔐',
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
                            What LaraBaseX Offers
                        </h2>
                        <p className="text-lg font-light text-gray-500 sm:text-xl dark:text-gray-400">
                            A comprehensive starter kit with everything you need to build modern web applications.
                        </p>
                    </div>
                    {/* Services Grid */}
                    <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:gap-10">
                        {services.map((service, index) => (
                            <div
                                key={index}
                                className="group flex flex-col rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:border-gray-300 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800/50 dark:hover:bg-gray-800"
                            >
                                {/* Icon */}
                                <div className="mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-gray-100 text-3xl transition-all duration-300 group-hover:bg-gray-900 group-hover:text-white dark:bg-gray-700">
                                    {service.icon}
                                </div>

                                {/* Content */}
                                <div className="flex-1">
                                    <h3 className="mb-3 text-xl font-semibold text-gray-900 dark:text-gray-100">{service.title}</h3>
                                    <p className="leading-relaxed text-gray-600 dark:text-gray-300">{service.description}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
