import Header from './Header';
import Footer from './Footer';

export default function PageSkeleton() {
  return (
    <div className="min-h-screen bg-[#f3f6f9] flex flex-col">
      <Header />
      <main className="flex-1 py-16 px-4">
        <div className="container mx-auto max-w-7xl animate-pulse space-y-12">
            {/* Header placeholder */}
            <div className="space-y-4 max-w-3xl mx-auto text-center flex flex-col items-center">
               <div className="h-12 sm:h-16 bg-slate-200 rounded-xl w-3/4"></div>
               <div className="h-6 bg-slate-200 rounded-md w-1/2"></div>
            </div>
            
            {/* Content placeholder grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {[...Array(8)].map((_, i) => (
                    <div key={i} className="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm h-80 flex flex-col gap-4">
                        <div className="flex-1 bg-slate-200 rounded-xl"></div>
                        <div className="h-6 bg-slate-200 rounded-md w-3/4"></div>
                        <div className="h-4 bg-slate-200 rounded-md w-1/2"></div>
                    </div>
                ))}
            </div>
        </div>
      </main>
      <Footer />
    </div>
  );
}
