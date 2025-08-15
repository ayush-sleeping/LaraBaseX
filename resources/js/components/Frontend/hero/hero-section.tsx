import { Button } from '@/components/ui/button';

export function HeroSection() {
    return (
        <section className="flex flex-col items-center justify-center px-4 py-20 text-center">
            {/* Tag */}
            <span className="mb-4 inline-block rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-700">New Release</span>
            {/* Heading */}
            <h1 className="max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">This is a heading for your new project</h1>
            {/* Subheading */}
            <p className="mt-6 max-w-2xl text-lg text-gray-500">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Elig doloremque mollitia fugiat omnis! Porro facilis quo animi consequatur.
                Explicabo.
            </p>
            {/* Buttons */}
            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                <Button size="lg">Get Started</Button>
                <Button size="lg" variant="outline">
                    Learn More
                </Button>
            </div>
            {/* Logos row */}
            <div className="mt-12 flex flex-wrap items-center justify-center gap-8 text-gray-500">
                <span className="flex items-center gap-2">
                    {/* Replace with your logo SVGs or icons */}
                    <span className="h-4 w-4 rounded-sm bg-black" /> shadcn/ui
                </span>
                <span className="flex items-center gap-2">
                    <span className="h-4 w-4 rounded-sm bg-black" /> Vercel
                </span>
                <span className="flex items-center gap-2">
                    <span className="h-4 w-4 rounded-sm bg-black" /> Supabase
                </span>
                <span className="flex items-center gap-2">
                    <span className="h-4 w-4 rounded-sm bg-black" /> Tailwind CSS
                </span>
            </div>
        </section>
    );
}
