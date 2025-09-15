import { Button } from '@/components/ui/button';

export function HeroSection() {
    return (
        <section className="flex flex-col items-center justify-center px-4 py-20 text-center">
            {/* Tag */}
            <span className="mb-4 inline-block rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700">
                Laravel + React Starter Kit
            </span>
            {/* Heading */}
            <h1 className="max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">Build Modern Web Apps with LaraBaseX</h1>
            {/* Subheading */}
            <p className="mt-6 max-w-2xl text-lg text-gray-500">
                A powerful Laravel starter kit featuring React, Inertia.js, TypeScript, and Shadcn UI. Get your full-stack application up and running
                in minutes with authentication, admin panel, and modern development tools.
            </p>
            {/* Buttons */}
            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                <Button size="lg">Get Started</Button>
                <Button size="lg" variant="outline">
                    View Documentation
                </Button>
            </div>
            {/* Tech Stack Section */}
            <div className="mt-16 w-full max-w-4xl">
                <p className="mb-8 text-sm font-medium tracking-wider text-gray-400 uppercase">Built with modern technologies</p>
                <div className="grid grid-cols-2 gap-6 md:grid-cols-5 lg:gap-8">
                    <div className="flex flex-col items-center space-y-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-black">
                            <span className="text-xs font-bold text-white">L</span>
                        </div>
                        <span className="text-sm font-medium text-gray-900">Laravel</span>
                    </div>
                    <div className="flex flex-col items-center space-y-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-black">
                            <span className="text-xs font-bold text-white">R</span>
                        </div>
                        <span className="text-sm font-medium text-gray-900">React</span>
                    </div>
                    <div className="flex flex-col items-center space-y-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-black">
                            <span className="text-xs font-bold text-white">I</span>
                        </div>
                        <span className="text-sm font-medium text-gray-900">Inertia.js</span>
                    </div>
                    <div className="flex flex-col items-center space-y-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-black">
                            <span className="text-xs font-bold text-white">TS</span>
                        </div>
                        <span className="text-sm font-medium text-gray-900">TypeScript</span>
                    </div>
                    <div className="flex flex-col items-center space-y-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all hover:shadow-md md:col-span-1">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-black">
                            <span className="text-xs font-bold text-white">UI</span>
                        </div>
                        <span className="text-sm font-medium text-gray-900">Shadcn UI</span>
                    </div>
                </div>
            </div>
        </section>
    );
}
