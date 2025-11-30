export function Footer() {
    return (
        <footer className="bg-gray-50 border-t mt-auto">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {/* About */}
                    <div>
                        <h3 className="font-semibold text-gray-900 mb-4">AI Quizzer</h3>
                        <p className="text-gray-600 text-sm">
                            An intelligent quiz platform powered by AI for enhanced learning and assessment.
                        </p>
                    </div>

                    {/* Developed By */}
                    <div>
                        <h3 className="font-semibold text-gray-900 mb-4">Developed and maintained by :</h3>
                        <p className="text-gray-600 text-sm leading-relaxed">
                            Centre of Excellence in Applied AI<br />
                            and VibeAI - The AI Coders Club<br />
                            IHRD College of Applied Science<br />
                            Vattamkulam
                        </p>
                    </div>

                    {/* Contact */}
                    <div>
                        <h3 className="font-semibold text-gray-900 mb-4">Contact</h3>
                        <ul className="space-y-2 text-sm text-gray-600">
                            <li>Email: vibeai@casvattamkulam.ihrd.ac.in</li>
                            <li>Phone: 8547006802</li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    );
}
