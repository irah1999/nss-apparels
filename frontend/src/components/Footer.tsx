import { Link } from 'react-router-dom';

export default function Footer() {
  return (
    <footer className="bg-[#1a233a] border-t border-white/10 px-4 py-10 mt-auto relative z-10 w-full">
      <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
        <div className="flex items-center gap-3">
           <div className="w-10 h-10 bg-[#d4af37] rounded-md flex items-center justify-center shadow-lg">
              <span className="text-[#1a233a] font-bold text-2xl italic leading-none" style={{ fontFamily: 'serif' }}>N</span>
            </div>
          <span className="text-2xl font-bold tracking-wider text-white" style={{ fontFamily: 'Arial, sans-serif' }}>
            NSS APPARELS
          </span>
        </div>
        
        <div className="flex flex-col md:flex-row items-center gap-4 text-sm text-slate-400">
           <p className="text-center md:text-left">
             &copy; {new Date().getFullYear()} NSS APPARELS. All rights reserved.
           </p>
           <div className="hidden md:block w-px h-4 bg-slate-600"></div>
           <p className="text-center md:text-left">
              Wholesale Dry Fit T-Shirts
           </p>
           <div className="hidden md:block w-px h-4 bg-slate-600"></div>
           <Link to="/contact" className="hover:text-yellow-400 font-bold transition-colors text-center md:text-left">
              Contact Us
           </Link>
        </div>
      </div>
    </footer>
  );
}
