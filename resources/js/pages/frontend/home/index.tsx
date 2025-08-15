import { HeroSection } from '@/components/Frontend/hero/hero-section';
import { Navbar } from '@/components/Frontend/navbar';

export default function Example() {
    return (
        <div className="relative w-full">
            <Navbar />
            <HeroSection />
        </div>
    );
}
