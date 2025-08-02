package cgm

import jakarta.servlet.annotation.WebServlet
import jakarta.servlet.http.HttpServlet
import jakarta.servlet.http.HttpServletRequest
import jakarta.servlet.http.HttpServletResponse

@WebServlet(urlPatterns = Array("/api/v4/health"))
class HealthApiServlet extends HttpServlet:
  override def doGet(
      req: HttpServletRequest,
      res: HttpServletResponse
  ): Unit =
    res.setStatus(200)
