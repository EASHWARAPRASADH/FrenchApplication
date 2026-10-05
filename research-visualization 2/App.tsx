/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
*/

import React, { useState, useEffect } from 'react';
import { HeroScene } from './components/QuantumScene';
import { RoadmapDiagram, RubricDiagram, PerformanceMetricDiagram } from './components/Diagrams';
// Added CheckCircle to the lucide-react imports
import { ArrowDown, Menu, X, BookOpen, GraduationCap, MapPin, Target, Users, CheckCircle, Star, Quote, ChevronRight, Globe, Smartphone, Flag } from 'lucide-react';
import logo from './logooo.png';
import { FrenchQuiz } from './components/FrenchQuiz';

// Asset imports
import arun from './src/assets/arun.png';
import akansha from './src/assets/akansha.jpg';
import cyrille from './src/assets/cyrille.png';
import yusurf from './src/assets/yusurf.jpg';
import discoverFrance1 from './src/assets/discover-france-1.jpeg';
import discoverFrance2 from './src/assets/discover-france-2.jpeg';
import discoverFrance3 from './src/assets/discover-france-3.jpeg';
import discoverFrance4 from './src/assets/discover-france-4.jpeg';

const CourseCard = ({ cefr, clb, title, subtitle, duration, lessons, delay }: { cefr: string, clb: string, title: string, subtitle: string, duration: string, lessons: string, delay: string }) => (
  <div className="flex flex-col animate-fade-in-up p-8 bg-white rounded-xl border border-blue-50 shadow-sm hover:shadow-lg transition-all duration-500 hover:border-ts-blue/50 group" style={{ animationDelay: delay }}>
    <div className="flex justify-between items-start mb-6">
      <div className="px-3 py-1 bg-ts-dark text-white text-[10px] font-bold tracking-widest uppercase rounded-full">TCF Canada {cefr}</div>
      <div className="text-ts-blue font-serif italic text-sm">{clb}</div>
    </div>
    <h3 className="font-serif text-xl text-ts-dark mb-2 group-hover:text-ts-blue transition-colors">{title}</h3>
    <p className="text-xs text-ts-dark/50 font-bold uppercase tracking-widest mb-6 leading-tight">{subtitle}</p>
    <div className="mt-auto pt-6 border-t border-blue-50 flex justify-between text-[11px] font-medium text-ts-blue/40 uppercase tracking-tighter">
      <span>{duration}</span>
      <span>{lessons}</span>
    </div>
  </div>
);

const TestimonialCard = ({ name, role, quote, result, delay }: { name: string, role: string, quote: string, result: string, delay: string }) => (
  <div className="bg-white p-8 rounded-xl border border-blue-50 animate-fade-in-up shadow-sm hover:shadow-md transition-all duration-500" style={{ animationDelay: delay }}>
    <div className="flex items-center gap-4 mb-6">
      <div className="w-12 h-12 bg-ts-dark rounded-full flex items-center justify-center text-white font-serif text-xl uppercase">{name[0]}</div>
      <div>
        <h4 className="font-serif text-lg text-ts-dark leading-none mb-1">{name}</h4>
        <p className="text-[10px] text-ts-dark/50 uppercase tracking-widest font-bold">{role}</p>
      </div>
    </div>
    <div className="flex gap-1 mb-4 text-ts-cyan">
      {[...Array(5)].map((_, i) => <Star key={i} size={12} fill="currentColor" />)}
    </div>
    <p className="text-ts-dark/70 italic font-serif text-base mb-6 leading-relaxed">"{quote}"</p>
    <div className="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-ts-blue text-[10px] font-bold tracking-widest uppercase rounded-full border border-blue-100">
      <Target size={12} /> {result}
    </div>
  </div>
);

const InstructorCard = ({ name, role, img, delay }: { name: string, role: string, img: string, delay: string }) => (
  <div className="bg-white rounded-xl border border-blue-50 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 animate-fade-in-up group" style={{ animationDelay: delay }}>
    <div className="h-72 overflow-hidden relative">
      <img src={img} alt={name} className="w-full h-full object-cover md:grayscale md:opacity-90 md:group-hover:grayscale-0 group-hover:scale-105 transition-all duration-700" />
      <div className="absolute inset-0 md:bg-ts-dark/10 md:group-hover:bg-transparent transition-colors duration-500"></div>
    </div>
    <div className="p-6 text-center border-t-2 border-transparent group-hover:border-ts-blue transition-colors">
      <h4 className="font-serif text-xl text-ts-dark mb-1">{name}</h4>
      <p className="text-ts-blue font-bold text-[10px] uppercase tracking-[0.2em]">{role}</p>
    </div>
  </div>
);

const App: React.FC = () => {
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 50);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const scrollToSection = (id: string) => (e: React.MouseEvent) => {
    e.preventDefault();
    setMenuOpen(false);
    const element = document.getElementById(id);
    if (element) {
      const headerOffset = 100;
      const elementPosition = element.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
      window.scrollTo({ top: offsetPosition, behavior: "smooth" });
    }
  };

  return (
    <div className="min-h-screen bg-ts-light text-ts-dark selection:bg-ts-blue selection:text-white">

      {/* Navigation */}
      <nav className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${scrolled ? 'bg-ts-light/90 backdrop-blur-md shadow-sm py-4' : 'bg-transparent py-6'}`}>
        <div className="container mx-auto px-6 flex justify-between items-center">
          <div className="flex items-center gap-4 cursor-pointer" onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}>
            <img src={logo} alt="TS Language School" className="h-10 md:h-20 w-auto object-contain" />
            <span className={`font-serif font-bold text-lg tracking-wide transition-opacity hidden md:inline-block ${scrolled ? 'opacity-100' : 'opacity-0 md:opacity-100'}`}>
              TS LANGUAGE <span className="font-normal text-ts-blue/60">SCHOOL</span>
            </span>
          </div>

          <div className="hidden md:flex items-center gap-8 text-sm font-medium tracking-wide text-ts-dark/70">
            <a href="#courses" onClick={scrollToSection('courses')} className="hover:text-ts-blue transition-colors cursor-pointer uppercase">Courses</a>
            <a href="#method" onClick={scrollToSection('method')} className="hover:text-ts-blue transition-colors cursor-pointer uppercase">Method</a>
            <a href="#team" onClick={scrollToSection('team')} className="hover:text-ts-blue transition-colors cursor-pointer uppercase">Team</a>
            <a href="#success" onClick={scrollToSection('success')} className="hover:text-ts-blue transition-colors cursor-pointer uppercase">Success</a>
            <a href="#gallery" onClick={scrollToSection('gallery')} className="hover:text-ts-blue transition-colors cursor-pointer uppercase">Gallery</a>
            <a href="/login" className="hover:text-ts-blue transition-colors uppercase">Sign In</a>
            <a href="/register" className="px-6 py-2 bg-ts-dark text-white rounded-sm hover:bg-ts-blue transition-colors shadow-sm cursor-pointer uppercase text-xs font-bold tracking-widest">Apply</a>
          </div>

          <button
            type="button"
            className="md:hidden text-ts-dark p-3 -mr-3 cursor-pointer touch-manipulation relative z-[100]"
            onClick={(e) => {
              e.preventDefault();
              setMenuOpen(!menuOpen);
            }}
            aria-label="Toggle menu"
            aria-expanded={menuOpen}
          >
            {menuOpen ? <X className="pointer-events-none" /> : <Menu className="pointer-events-none" />}
          </button>
        </div>

        {/* Mobile Menu Overlay */}
        {menuOpen && (
          <div className="absolute top-full left-0 right-0 bg-ts-light border-b border-blue-100 shadow-xl md:hidden py-6 px-6 flex flex-col gap-6 animate-fade-in-up">
            <a href="#courses" onClick={scrollToSection('courses')} className="text-ts-dark/80 hover:text-ts-blue font-medium uppercase tracking-wide text-sm transition-colors cursor-pointer">Courses</a>
            <a href="#method" onClick={scrollToSection('method')} className="text-ts-dark/80 hover:text-ts-blue font-medium uppercase tracking-wide text-sm transition-colors cursor-pointer">Method</a>
            <a href="#success" onClick={scrollToSection('success')} className="text-ts-dark/80 hover:text-ts-blue font-medium uppercase tracking-wide text-sm transition-colors cursor-pointer">Success</a>
            <hr className="border-blue-100" />
            <a href="/login" className="text-ts-dark/80 hover:text-ts-blue font-medium uppercase tracking-wide text-sm transition-colors">Sign In</a>
            <a href="/register" className="px-6 py-3 bg-ts-dark text-white text-center rounded-sm hover:bg-ts-blue transition-colors uppercase text-xs font-bold tracking-widest mt-2 shadow-sm">Apply</a>
          </div>
        )}
      </nav>

      {/* Hero Section */}
      <header className="relative h-screen flex items-center justify-center overflow-hidden">
        <HeroScene />
        <div className="absolute inset-0 z-0 pointer-events-none bg-[radial-gradient(circle_at_center,rgba(240,247,255,0.9)_0%,rgba(240,247,255,0.5)_60%,rgba(240,247,255,0.2)_100%)]" />

        <div className="relative z-10 container mx-auto px-6 text-center lg:text-left">
          <div className="max-w-4xl">
            <div className="inline-block mb-6 px-4 py-1.5 border border-ts-blue/30 text-ts-blue text-[10px] tracking-[0.4em] uppercase font-bold rounded-full backdrop-blur-sm bg-white/40">
              Global French Certification
            </div>
            <h1 className="font-serif text-5xl md:text-8xl lg:text-9xl font-medium leading-[0.85] mb-10 text-ts-dark tracking-tighter">
              EXPERT <br /><span className="italic font-normal text-ts-blue/80 underline decoration-ts-cyan/30 underline-offset-8">FRENCH TRAINING</span>
            </h1>

            <div className="flex flex-col md:flex-row justify-start items-start md:items-center gap-8 mb-16">
              <div className="flex flex-col">
                <span className="text-3xl md:text-5xl font-serif text-ts-dark/40">MASTER</span>
                <span className="text-4xl md:text-6xl font-serif text-ts-blue font-bold tracking-tighter">FLUENCY</span>
              </div>
              <div className="h-20 w-[1px] bg-ts-blue/20 hidden md:block"></div>
              <p className="max-w-md text-base md:text-lg text-ts-dark/70 font-light leading-relaxed italic">
                "Advanced language programs designed for global professionals and students seeking certified proficiency in the French language."
              </p>
            </div>

            <div className="flex flex-wrap gap-8 text-[9px] font-bold tracking-[0.3em] text-ts-blue/40 uppercase">
              <span className="flex items-center gap-2 px-3 py-1 bg-blue-50 rounded-full border border-blue-100"><Target size={12} className="text-ts-cyan" /> DELF/DALF Prep</span>
              <span className="flex items-center gap-2 px-3 py-1 bg-blue-50 rounded-full border border-blue-100"><CheckCircle size={12} className="text-ts-cyan" /> CEFR Aligned</span>
              <span className="flex items-center gap-2 px-3 py-1 bg-blue-50 rounded-full border border-blue-100"><GraduationCap size={12} className="text-ts-cyan" /> Certified Experts</span>
            </div>
          </div>
        </div>

        {/* Marquee */}
        <div className="absolute bottom-0 w-full bg-ts-dark py-4 overflow-hidden border-t border-blue-900/30">
          <div className="animate-marquee">
            {[...Array(10)].map((_, i) => (
              <span key={i} className="text-white text-xs font-bold tracking-[0.2em] uppercase mx-8 opacity-60">
                ● DELF/DALF Prep ● TS Language School ● Global French Excellence
              </span>
            ))}
          </div>
        </div>
      </header>

      <main>
        {/* 1. Trust & Performance Section (Combined) */}
        <section className="py-24 bg-ts-dark text-white overflow-hidden relative border-b border-blue-900/30">
          <div className="absolute inset-0 opacity-10 bg-[url('https://images.unsplash.com/photo-1502602898657-3e907a5ea82c?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center"></div>
          <div className="container mx-auto px-6 relative z-10">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
              <div>
                <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-cyan uppercase">The TS Advantage</div>
                <h2 className="font-serif text-4xl md:text-5xl mb-8 leading-tight">Elite Performance <br /><span className="italic font-normal text-ts-cyan/50">Guaranteed Results</span></h2>
                <div className="grid grid-cols-2 gap-8 mb-12">
                  <div>
                    <div className="font-serif text-4xl text-ts-cyan mb-1">6+</div>
                    <div className="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Years of Excellence</div>
                  </div>
                  <div>
                    <div className="font-serif text-4xl text-ts-cyan mb-1">3K+</div>
                    <div className="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Happy Learners</div>
                  </div>
                  <div>
                    <div className="font-serif text-4xl text-ts-cyan mb-1">92%</div>
                    <div className="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Certification Rate</div>
                  </div>
                  <div>
                    <div className="font-serif text-4xl text-ts-cyan mb-1">4.8/5</div>
                    <div className="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Student Rating</div>
                  </div>
                </div>
                <button className="px-8 py-4 bg-ts-blue text-white font-bold text-xs uppercase tracking-widest rounded-sm hover:bg-ts-cyan transition-all shadow-xl">
                  Get Free Level Assessment
                </button>
              </div>
              <div className="relative">
                <PerformanceMetricDiagram />
                <div className="absolute -bottom-6 -right-6 p-6 bg-white text-ts-dark rounded-xl shadow-2xl max-w-xs border border-blue-50">
                  <p className="text-[10px] font-bold uppercase tracking-widest mb-2 text-ts-blue">Data Driven</p>
                  <p className="text-sm font-light italic">"Our students are 40% more likely to achieve target proficiency levels on their first attempt."</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* 2. Course Levels Grid (What we offer) */}
        <section id="courses" className="py-24 bg-white">
          <div className="container mx-auto px-6">
            <div className="text-center mb-20">
              <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-blue uppercase">Target CLB 7+</div>
              <h2 className="font-serif text-4xl md:text-5xl mb-6 text-ts-dark">Choose Your CLB Level</h2>
              <p className="text-ts-dark/60 max-w-2xl mx-auto">Each CEFR level maps to Canadian Language Benchmarks (CLB) for immigration purposes. Find your starting point for Express Entry.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <CourseCard cefr="A1" clb="CLB 1-2" title="Beginner" subtitle="Foundation for Express Entry" duration="8 weeks" lessons="32 lessons" delay="0s" />
              <CourseCard cefr="A2" clb="CLB 3-4" title="Elementary" subtitle="Minimum for some PNP Programs" duration="10 weeks" lessons="40 lessons" delay="0.1s" />
              <CourseCard cefr="B1" clb="CLB 5-6" title="Intermediate" subtitle="Strengthens Express Entry Profile" duration="12 weeks" lessons="48 lessons" delay="0.2s" />
              <CourseCard cefr="B2" clb="CLB 7-8" title="Upper Intermediate" subtitle="Target for Express Entry (Recommended)" duration="14 weeks" lessons="56 lessons" delay="0.3s" />
              <CourseCard cefr="C1" clb="CLB 9-10" title="Advanced" subtitle="Maximum CRS Points Boost" duration="16 weeks" lessons="64 lessons" delay="0.4s" />
              <CourseCard cefr="C2" clb="CLB 11-12" title="Mastery" subtitle="Elite Proficiency Recognition" duration="18 weeks" lessons="72 lessons" delay="0.5s" />
            </div>
          </div>
        </section>

        {/* 3. Success Stories (Proof it works) */}
        <section id="success" className="py-24 bg-ts-light border-y border-blue-100">
          <div className="container mx-auto px-6">
            <div className="text-center mb-16">
              <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-blue uppercase">Student Success</div>
              <h2 className="font-serif text-4xl md:text-5xl mb-4 text-ts-dark">Real Results</h2>
              <p className="text-ts-dark/60">Global learners achieving linguistic excellence. Our students' journeys speak for themselves.</p>
            </div>

            <div className="relative">
              <div className="flex overflow-x-auto gap-8 pb-12 snap-x no-scrollbar">
                {[
                  { name: "Swedha Sri", role: "Google Review • DELF Candidate", quote: "Highly recommended for learning French and clearing certification exams. The trainers are very supportive and the environment is perfect for learning.", result: "DELF Success" },
                  { name: "Fahima", role: "Google Review • Language Aspirant", quote: "TS Language School stands out for its high-quality French training and personalized guidance. The coaching is systematic and focused, helping students achieve their goals efficiently.", result: "Certified" },
                  { name: "Yuvaraj S", role: "Google Review • Language Learner", quote: "TS Language School provides outstanding support for French learning success. Their commitment to quality education makes them a reliable partner for achieving proficiency.", result: "Proficiency Ready" },
                  { name: "Shivani Dharsika", role: "Google Review • Student", quote: "TS Language School provides a comfortable environment for learning languages. The staff are supportive and teach in a very simple way. I gained better confidence in speaking after joining here.", result: "Confidence Boost" },
                  { name: "Shri Mathi M", role: "Google Review • Beginner", quote: "The classes are interactive and easy to understand. Excellent for French beginners. Highly professional training with real-time practice.", result: "Foundations Built" },
                  { name: "Dhipak Sankar", role: "Google Review • Exam Candidate", quote: "Strong focus on speaking and writing skills. Perfect for exam success. Highly professional training with real-time practice.", result: "Exam Ready" }
                ].map((t, i) => (
                  <div key={i} className="min-w-[320px] md:min-w-[400px] snap-center">
                    <TestimonialCard
                      name={t.name}
                      role={t.role}
                      quote={t.quote}
                      result={t.result}
                      delay={`${i * 0.1}s`}
                    />
                  </div>
                ))}
              </div>
              
              {/* Slider Indicators */}
              <div className="flex justify-center gap-2 mt-4">
                {[...Array(6)].map((_, i) => (
                  <div key={i} className={`w-1.5 h-1.5 rounded-full ${i === 0 ? 'bg-ts-cyan' : 'bg-blue-200'}`}></div>
                ))}
              </div>
            </div>
          </div>
        </section>

        {/* 4. Methodology: The Matrix (How it works) */}
        <section id="method" className="py-24 bg-white border-b border-blue-50">
          <div className="container mx-auto px-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
              <div>
                <div className="inline-flex items-center gap-2 px-3 py-1 bg-white text-ts-blue text-xs font-bold tracking-widest uppercase rounded-full mb-6 border border-blue-100 shadow-sm">
                  <BookOpen size={14} /> TS METHOD
                </div>
                <h2 className="font-serif text-4xl md:text-5xl mb-8 text-ts-dark leading-tight">Mastery-First <br />Methodology</h2>
                <div className="space-y-8">
                  <div className="flex gap-4">
                    <div className="w-12 h-12 rounded-full bg-ts-dark flex items-center justify-center text-white shrink-0"><Target size={20} /></div>
                    <div>
                      <h4 className="font-serif text-xl mb-1 text-ts-dark">Rubric Alignment</h4>
                      <p className="text-ts-dark/60 text-sm leading-relaxed">Curriculum rigorously aligned with international French proficiency rubrics and CEFR descriptors.</p>
                    </div>
                  </div>
                  <div className="flex gap-4">
                    <div className="w-12 h-12 rounded-full bg-ts-dark flex items-center justify-center text-white shrink-0"><MapPin size={20} /></div>
                    <div>
                      <h4 className="font-serif text-xl mb-1 text-ts-dark">Global Context</h4>
                      <p className="text-ts-dark/60 text-sm leading-relaxed">Integrated cultural context specifically for living and working in Francophone countries.</p>
                    </div>
                  </div>
                  <div className="flex gap-4">
                    <div className="w-12 h-12 rounded-full bg-ts-dark flex items-center justify-center text-white shrink-0"><Users size={20} /></div>
                    <div>
                      <h4 className="font-serif text-xl mb-1 text-ts-dark">Fluency Guarantee</h4>
                      <p className="text-ts-dark/60 text-sm leading-relaxed">Structured roadmap focused on practical communication and official certification.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div className="relative">
                <RoadmapDiagram />
                <div className="absolute -bottom-6 -right-6 p-6 bg-ts-dark text-white rounded-xl shadow-xl max-w-xs">
                  <p className="text-xs font-bold uppercase tracking-[0.2em] mb-2 text-ts-cyan">Expert Insight</p>
                  <p className="text-sm font-light italic">"Most learners plateau at intermediate levels. We use cognitive linguistic techniques to push you to native-level fluency."</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* 5. Team Section (Who teaches) */}
        <section id="team" className="py-24 bg-ts-light border-b border-blue-50">
          <div className="container mx-auto px-6">
            <div className="text-center mb-20">
              <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-blue uppercase">Expert Faculty</div>
              <h2 className="font-serif text-4xl md:text-5xl mb-6 text-ts-dark">Passionate Instructors</h2>
              <p className="text-ts-dark/60 max-w-2xl mx-auto">Learn from certified examiners and advanced language specialists dedicated to your success.</p>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
              <InstructorCard name="Arun" role="Head of French Dept." img={arun} delay="0s" />
              <InstructorCard name="Akansha Arora" role="Senior Instructor" img={akansha} delay="0.1s" />
              <InstructorCard name="Yusuf Fayas" role="Proficiency Specialist" img={yusurf} delay="0.2s" />
              <InstructorCard name="Cyrille Helena" role="DALF Expert" img={cyrille} delay="0.3s" />
            </div>
          </div>
        </section>

        {/* 6. Interactive Quiz (Engagement) */}
        <section className="py-24 bg-ts-dark overflow-hidden relative">
          <div className="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,rgba(0,196,255,0.2)_0%,transparent_70%)]"></div>
          <div className="container mx-auto px-6 relative z-10">
             <div className="max-w-4xl mx-auto">
               <div className="text-center mb-12">
                 <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-cyan uppercase">Interactive</div>
                 <h2 className="font-serif text-4xl text-white">Test Your Knowledge</h2>
               </div>
               <div className="bg-white rounded-2xl shadow-2xl overflow-hidden border border-blue-900/20">
                 <FrenchQuiz />
               </div>
             </div>
          </div>
        </section>

        {/* 7. Specialities Section (Expansion) */}
        <section className="py-24 bg-white border-b border-blue-50">
          <div className="container mx-auto px-6">
            <div className="text-center mb-20">
              <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-blue uppercase">Our Philosophy</div>
              <h2 className="font-serif text-4xl md:text-5xl mb-6 text-ts-dark">Learning Built for Everyone</h2>
            </div>
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <div className="bg-ts-light p-12 rounded-xl border border-blue-100 hover:shadow-lg transition-all duration-500">
                <div className="p-3 bg-ts-dark text-ts-cyan rounded-sm w-fit mb-8">
                  <Users size={24} />
                </div>
                <h4 className="font-serif text-2xl text-ts-dark mb-6">For Every Age Group</h4>
                <p className="text-ts-dark/60 mb-8 leading-relaxed font-light text-lg italic">
                  "Tailored curriculums designed exactly for your cognitive and developmental needs, from children to professionals."
                </p>
                <ul className="space-y-4 text-ts-dark text-sm font-medium">
                  <li className="flex items-center gap-3"><CheckCircle size={16} className="text-ts-blue" /> Kids Programs (Ages 6-12)</li>
                  <li className="flex items-center gap-3"><CheckCircle size={16} className="text-ts-blue" /> Teens Prep (Ages 13-17)</li>
                  <li className="flex items-center gap-3"><CheckCircle size={16} className="text-ts-blue" /> Adults & Professionals (18+)</li>
                </ul>
              </div>
              <div className="bg-ts-light p-12 rounded-xl border border-blue-100 hover:shadow-lg transition-all duration-500">
                <div className="p-3 bg-ts-dark text-ts-cyan rounded-sm w-fit mb-8">
                  <GraduationCap size={24} />
                </div>
                <h4 className="font-serif text-2xl text-ts-dark mb-6">Certification & Careers</h4>
                <p className="text-ts-dark/60 mb-8 leading-relaxed font-light text-lg italic">
                  "In today's globalized economy, bilingualism is your ultimate competitive edge. We prepare you to certify your proficiency."
                </p>
                <ul className="space-y-4 text-ts-dark text-sm font-medium">
                  <li className="flex items-center gap-3"><CheckCircle size={16} className="text-ts-blue" /> Career Mobility</li>
                  <li className="flex items-center gap-3"><CheckCircle size={16} className="text-ts-blue" /> Globally Recognised Certificates</li>
                  <li className="flex items-center gap-3"><CheckCircle size={16} className="text-ts-blue" /> Enhanced Opportunities</li>
                </ul>
              </div>
            </div>
          </div>
        </section>

        {/* FAQ Section */}
        <section className="py-24 bg-ts-light">
          <div className="container mx-auto px-6">
            <div className="text-center mb-20">
              <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-blue uppercase">Common Questions</div>
              <h2 className="font-serif text-4xl md:text-5xl text-ts-dark">Frequently Asked</h2>
            </div>
            <div className="max-w-3xl mx-auto space-y-6">
              {[
                { q: "Are the classes online or offline?", a: "We offer both online interactive sessions and in-person classes. Both formats follow the same rigorous CEFR-aligned curriculum." },
                { q: "Do you offer official certifications?", a: "We prepare you for globally recognized exams like DELF, DALF, TCF, and TEF. Upon course completion, you'll be fully equipped to clear these certifications." },
                { q: "What is the duration of the courses?", a: "Course durations range from 8 to 18 weeks depending on your target CEFR level (A1 to C2)." },
                { q: "How do I know my current French level?", a: "We provide a free 15-minute diagnostic assessment to evaluate your current proficiency and recommend the right starting level for your goals." }
              ].map((faq, i) => (
                <div key={i} className="p-8 bg-white border border-blue-50 rounded-xl shadow-sm">
                  <h4 className="font-serif text-lg text-ts-dark mb-4">{faq.q}</h4>
                  <p className="text-ts-dark/60 text-sm leading-relaxed font-light">{faq.a}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* France Gallery Section (Aesthetics) */}
        <section id="gallery" className="py-24 bg-white border-t border-blue-50">
          <div className="container mx-auto px-6">
            <div className="text-center mb-20">
              <div className="inline-block mb-4 text-xs font-bold tracking-[0.3em] text-ts-blue uppercase">Immersion</div>
              <h2 className="font-serif text-4xl md:text-5xl mb-6 text-ts-dark">Discover France</h2>
              <p className="text-ts-dark/60 max-w-2xl mx-auto">Immerse yourself in French culture, art, architecture, and lifestyle.</p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div className="col-span-1 md:col-span-2 h-80 relative rounded-xl overflow-hidden group border border-blue-100">
                  <img src={discoverFrance1} alt="Paris" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                  <div className="absolute inset-0 bg-ts-dark/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
                    <h4 className="font-serif text-2xl text-white">Paris — Tour Eiffel</h4>
                  </div>
              </div>
              <div className="col-span-1 h-80 relative rounded-xl overflow-hidden group border border-blue-100">
                  <img src={discoverFrance2} alt="Louvre" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                  <div className="absolute inset-0 bg-ts-dark/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
                    <h4 className="font-serif text-xl text-white">Musée du Louvre</h4>
                  </div>
              </div>
              <div className="col-span-1 h-80 relative rounded-xl overflow-hidden group border border-blue-100">
                  <img src={discoverFrance3} alt="Cote d'Azur" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                  <div className="absolute inset-0 bg-ts-dark/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
                    <h4 className="font-serif text-xl text-white">Côte d'Azur</h4>
                  </div>
              </div>
              <div className="col-span-1 md:col-span-2 h-80 relative rounded-xl overflow-hidden group border border-blue-100">
                  <img src={discoverFrance4} alt="Alpes" className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                  <div className="absolute inset-0 bg-ts-dark/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
                    <h4 className="font-serif text-2xl text-white">Alpes Françaises</h4>
                  </div>
              </div>
            </div>
          </div>
        </section>

      </main>

      <footer className="bg-ts-dark text-blue-200/50 py-16">
        <div className="container mx-auto px-6">
          <div className="flex flex-col md:flex-row justify-between items-start gap-12 mb-16">
            <div className="max-w-xs">
              <div className="text-white font-serif font-bold text-2xl mb-4">TS Language Platform</div>
              <p className="text-sm leading-relaxed mb-6">Master French with our comprehensive online learning platform designed for all levels.</p>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-4 gap-12 text-sm">
              <div>
                <h4 className="text-white font-serif text-lg mb-4">LEARN</h4>
                <ul className="space-y-2">
                  <li><a href="/courses" className="hover:text-ts-cyan transition-colors">Courses</a></li>
                  <li><a href="/dashboard" className="hover:text-ts-cyan transition-colors">Lessons</a></li>
                  <li><a href="/test" className="hover:text-ts-cyan transition-colors">Tests</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Resources</a></li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-serif text-lg mb-4">SUPPORT</h4>
                <ul className="space-y-2">
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Help Center</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Contact Us</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">FAQ</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Community</a></li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-serif text-lg mb-4">COMPANY</h4>
                <ul className="space-y-2">
                  <li><a href="/about" className="hover:text-ts-cyan transition-colors">About Us</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Privacy Policy</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Terms of Service</a></li>
                  <li><a href="#" className="hover:text-ts-cyan transition-colors">Careers</a></li>
                </ul>
              </div>
              <div>
                <h4 className="text-white font-serif text-lg mb-4">NEWSLETTER</h4>
                <p className="text-sm mb-4">Stay updated with our latest courses and tips</p>
                <div className="flex gap-2">
                  <input type="email" placeholder="Enter email" className="bg-ts-dark/50 border border-blue-900/30 text-white text-xs p-2 rounded-sm w-full focus:ring-1 focus:ring-ts-cyan" />
                  <button className="bg-ts-blue text-white px-3 py-2 text-xs font-bold rounded-sm uppercase">Subscribe</button>
                </div>
              </div>
            </div>
          </div>
          <div className="pt-8 border-t border-blue-900/30 text-center text-[10px] uppercase tracking-[0.2em] font-medium">
            © 2026 TS Language Platform. All rights reserved.
          </div>
        </div>
      </footer>
    </div>
  );
};

export default App;