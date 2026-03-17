import { Link } from 'react-router-dom';
import { MessageCircle } from 'lucide-react';

export default function Header() {
  return (
    <header className="sticky top-0 z-50 bg-[#1a233a] text-white border-t-4 border-[#d4af37] shadow-md">
      <div className="container mx-auto px-4 lg:px-8 max-w-7xl h-16 sm:h-20 flex items-center justify-between">
        <Link to="/" className="flex items-center gap-3">
          <div className="w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-md flex items-center justify-center">
            <span className="text-[#1a233a] font-bold text-xl sm:text-2xl italic leading-none" style={{ fontFamily: 'serif' }}>N</span>
          </div>
          <span className="text-xl sm:text-2xl font-bold tracking-wider" style={{ fontFamily: 'Arial, sans-serif' }}>
            NSS APPARELS
          </span>
        </Link>

        <div className="flex items-center gap-4 sm:gap-6">
          <nav className="hidden md:flex gap-6">
            <Link to="/" className="hover:text-yellow-400 font-medium transition-colors">Home</Link>
            <Link to="/products" className="hover:text-yellow-400 font-medium transition-colors">Products</Link>
            <Link to="/contact" className="hover:text-yellow-400 font-medium transition-colors">Contact</Link>
          </nav>
          <a href={`https://wa.me/${import.meta.env.VITE_WHATSAPP_NUMBER || '918098760720'}?text=Hello%20NSS%20APPARELS%20I%20want%20catalog`} className="bg-[#5c9841] hover:bg-[#4a7a34] text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full flex items-center gap-2 text-sm sm:text-base font-medium transition-colors border border-[#6ab04c]">
            <MessageCircle className="w-4 h-4 sm:w-5 sm:h-5" />
            <span className="hidden sm:inline">Chat with Us</span>
            <span className="sm:hidden">Chat</span>
          </a>
        </div>
      </div>
    </header>
  );
}
